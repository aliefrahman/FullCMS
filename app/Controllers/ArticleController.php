<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ArticleTag;
use App\Helpers\Session;
use App\Helpers\Auth;

class ArticleController extends Controller
{
    private $articleModel;
    private $categoryModel;
    private $tagModel;
    private $articleTagModel;

    public function __construct()
    {
        // Require basic capability to write articles
        Auth::requirePermission('create_articles');

        $this->articleModel = new Article();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
        $this->articleTagModel = new ArticleTag();
    }

    public function index()
    {
        $canEditOthers = Auth::hasPermission('edit_articles');
        $currentUserId = Session::get('user_id');

        // Isolation scoped fetch
        $articles = $this->articleModel->getAll($canEditOthers, $currentUserId);

        $data = [
            'articles' => $articles,
            'title' => 'Manajemen Artikel',
            'activePage' => 'articles',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/articles/index', $data);
    }

    public function create()
    {
        $categories = $this->categoryModel->getAll();
        $tags = $this->tagModel->getAll();

        $data = [
            'categories' => $categories,
            'tags' => $tags,
            'title' => 'Tulis Artikel Baru',
            'activePage' => 'articles',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/articles/create', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        $title = \App\Helpers\Security::sanitize(trim($_POST['title'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $content = trim($_POST['content'] ?? ''); // Quill editor content (safe semantic HTML)
        $categoryId = intval($_POST['category_id'] ?? 0);
        $status = \App\Helpers\Security::sanitize($_POST['status'] ?? 'draft');
        $authorId = Session::get('user_id');
        $featuredImageCaption = \App\Helpers\Security::sanitize(trim($_POST['featured_image_caption'] ?? ''));

        if (empty($title) || empty($content)) {
            Session::flash('old_input', $_POST);
            Session::flash('error', 'Judul dan konten artikel wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/articles/create');
            exit;
        }

        // Enforce Publishing capability check
        if (!Auth::hasPermission('publish_articles')) {
            $status = 'draft'; // Force draft if not allowed to publish
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        // Validate unique slug
        if ($this->articleModel->checkSlugExists($slug)) {
            $slug = $slug . '-' . time(); // Auto-append timestamp to prevent collision
        }

        // 6. Secure Image Upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = \App\Helpers\Security::secureUpload($_FILES['featured_image']);
            if ($uploadResult['success']) {
                $featuredImage = $uploadResult['filename'];
            } else {
                Session::flash('old_input', $_POST);
                Session::flash('error', 'Gagal mengunggah gambar: ' . $uploadResult['message']);
                header('Location: ' . PUBLIC_URL . '/admin/articles/create');
                exit;
            }
        }

        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        $saveData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'category_id' => $categoryId,
            'featured_image' => $featuredImage,
            'featured_image_caption' => $featuredImageCaption,
            'author_id' => $authorId,
            'status' => $status,
            'published_at' => $publishedAt
        ];

        $articleId = $this->articleModel->create($saveData);

        if ($articleId) {
            // Sync tags relations using ArticleTag Model
            $selectedTags = $_POST['tags'] ?? [];
            $this->articleTagModel->sync($articleId, $selectedTags);

            // 8. Audit Logging
            \App\Helpers\Security::logAudit('ARTICLE_CREATED', 'Article created successfully: ' . $title);

            Session::flash('success', 'Artikel berhasil disimpan.');
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        } else {
            Session::flash('old_input', $_POST);
            Session::flash('error', 'Gagal menyimpan artikel.');
            header('Location: ' . PUBLIC_URL . '/admin/articles/create');
            exit;
        }
    }

    public function edit()
    {
        $id = intval($_GET['id'] ?? 0);
        $article = $this->articleModel->getById($id);

        if (!$article) {
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        // 7. Object-Level Authorization check
        if (!\App\Helpers\Security::canModifyArticle($article->author_id)) {
            \App\Helpers\Security::logAudit('UNAUTHORIZED_ARTICLE_ACCESS_ATTEMPT', 'Attempted unauthorized edit of article ID: ' . $id);
            Session::flash('error', 'Akses ditolak: Anda tidak dapat mengedit artikel milik orang lain.');
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $tags = $this->tagModel->getAll();

        // Fetch selected tag IDs using ArticleTag Model
        $selectedTags = $this->articleTagModel->getTagIdsByArticle($id);

        $data = [
            'article' => $article,
            'categories' => $categories,
            'tags' => $tags,
            'selectedTags' => $selectedTags,
            'title' => 'Edit Artikel',
            'activePage' => 'articles',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/articles/edit', $data);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $title = \App\Helpers\Security::sanitize(trim($_POST['title'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $content = trim($_POST['content'] ?? ''); // Quill editor content (safe semantic HTML)
        $categoryId = intval($_POST['category_id'] ?? 0);
        $status = \App\Helpers\Security::sanitize($_POST['status'] ?? 'draft');
        $featuredImageCaption = \App\Helpers\Security::sanitize(trim($_POST['featured_image_caption'] ?? ''));

        // Fetch existing article to verify ownership and old image
        $article = $this->articleModel->getById($id);

        if (!$article) {
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        // 7. Object-Level Authorization check
        if (!\App\Helpers\Security::canModifyArticle($article->author_id)) {
            \App\Helpers\Security::logAudit('UNAUTHORIZED_ARTICLE_ACCESS_ATTEMPT', 'Attempted unauthorized update of article ID: ' . $id);
            Session::flash('error', 'Akses ditolak: Anda tidak dapat mengedit artikel milik orang lain.');
            header('Location: ' . PUBLIC_URL . '/admin/articles');
            exit;
        }

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Judul dan konten wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/articles/edit?id=' . $id);
            exit;
        }

        // Publishing capabilities
        if (!Auth::hasPermission('publish_articles')) {
            $status = $article->status; // Keep existing status if no publish rights
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        // Check duplicate slug
        if ($this->articleModel->checkSlugExists($slug, $id)) {
            $slug = $slug . '-' . time();
        }

        // 6. Secure Image Update logic
        $featuredImage = $article->featured_image;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = \App\Helpers\Security::secureUpload($_FILES['featured_image']);
            if ($uploadResult['success']) {
                // Delete old image if it exists
                $uploadDir = __DIR__ . '/../../public/uploads/articles/';
                if ($featuredImage && file_exists($uploadDir . $featuredImage)) {
                    @unlink($uploadDir . $featuredImage);
                }
                $featuredImage = $uploadResult['filename'];
            } else {
                Session::flash('error', 'Gagal memperbarui gambar: ' . $uploadResult['message']);
                header('Location: ' . PUBLIC_URL . '/admin/articles/edit?id=' . $id);
                exit;
            }
        }

        $publishedAt = $article->published_at;
        if ($status === 'published' && $article->status !== 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $updateData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'category_id' => $categoryId,
            'featured_image' => $featuredImage,
            'featured_image_caption' => $featuredImageCaption,
            'status' => $status,
            'published_at' => $publishedAt
        ];

        if ($this->articleModel->update($id, $updateData)) {
            // Sync tags relations using ArticleTag Model
            $selectedTags = $_POST['tags'] ?? [];
            $this->articleTagModel->sync($id, $selectedTags);

            // 8. Audit Logging
            \App\Helpers\Security::logAudit('ARTICLE_UPDATED', 'Article updated successfully: ' . $title);

            Session::flash('success', 'Artikel berhasil diperbarui.');
        } else {
            Session::flash('error', 'Gagal memperbarui artikel.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/articles');
        exit;
    }

    public function delete()
    {
        $id = intval($_GET['id'] ?? 0);
        $article = $this->articleModel->getById($id);

        if ($article) {
            // 7. Object-Level Authorization
            if (!\App\Helpers\Security::canModifyArticle($article->author_id)) {
                \App\Helpers\Security::logAudit('UNAUTHORIZED_ARTICLE_ACCESS_ATTEMPT', 'Attempted unauthorized deletion of article ID: ' . $id);
                Session::flash('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus artikel ini.');
            } else {
                // Delete image file
                if ($article->featured_image) {
                    $imagePath = __DIR__ . '/../../public/uploads/articles/' . $article->featured_image;
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                }

                // Delete relations first using ArticleTag Model
                $this->articleTagModel->detachAll($id);

                // Delete article
                $this->articleModel->delete($id);

                // 8. Audit Logging
                \App\Helpers\Security::logAudit('ARTICLE_DELETED', 'Article deleted successfully ID: ' . $id);

                Session::flash('success', 'Artikel berhasil dihapus.');
            }
        }

        header('Location: ' . PUBLIC_URL . '/admin/articles');
        exit;
    }
}
