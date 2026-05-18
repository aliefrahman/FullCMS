<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ArticleTag;
use App\Core\Database;

class HomeController extends Controller
{
    private $articleModel;
    private $articleTagModel;

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->articleTagModel = new ArticleTag();
    }

    public function index()
    {
        $articles = $this->articleModel->getPublished();
        
        $this->view('frontend/home', [
            'title' => 'Beranda - NexusCMS Portal',
            'articles' => $articles
        ]);
    }

    public function read()
    {
        $slug = trim($_GET['slug'] ?? '');
        $article = null;

        if (!empty($slug)) {
            $article = $this->articleModel->getBySlug($slug);
        } else {
            $id = intval($_GET['id'] ?? 0);
            if ($id > 0) {
                $article = $this->articleModel->getById($id);
            }
        }

        // Verify article exists and is published
        if (!$article || $article->status !== 'published') {
            $this->view('error/404', ['title' => 'Artikel Tidak Ditemukan']);
            exit;
        }

        $db = new Database();
        
        // Fetch Category name and slug
        $db->query("SELECT name, slug FROM categories WHERE id = :id");
        $db->bind(':id', $article->category_id);
        $cat = $db->single();
        $article->category_name = $cat ? $cat->name : 'Tanpa Kategori';
        $article->category_slug = $cat ? $cat->slug : '';

        // Fetch Author name
        $db->query("SELECT full_name FROM users WHERE id = :id");
        $db->bind(':id', $article->author_id);
        $user = $db->single();
        $article->author_name = $user ? $user->full_name : 'Unknown Author';

        // Fetch associated tags
        $tagIds = $this->articleTagModel->getTagIdsByArticle($article->id);
        $tags = [];
        if (!empty($tagIds)) {
            $tagModel = new Tag();
            foreach ($tagIds as $tid) {
                $t = $tagModel->getById($tid);
                if ($t) {
                    $tags[] = $t;
                }
            }
        }

        // Fetch other recent published articles for sidebar
        $db->query("SELECT * FROM articles WHERE status = 'published' AND id != :id ORDER BY published_at DESC LIMIT 5");
        $db->bind(':id', $article->id);
        $recentArticles = $db->resultSet();

        // Fetch approved comments for this article
        $commentModel = new \App\Models\Comment();
        $comments = $commentModel->getApprovedByArticle($article->id);

        $this->view('frontend/read', [
            'title' => $article->title . ' - NexusCMS',
            'article' => $article,
            'tags' => $tags,
            'recentArticles' => $recentArticles,
            'comments' => $comments
        ]);
    }

    public function page()
    {
        $slug = trim($_GET['slug'] ?? '');
        
        if (empty($slug)) {
            $this->view('error/404', ['title' => 'Halaman Tidak Ditemukan']);
            exit;
        }

        $pageModel = new \App\Models\Page();
        $page = $pageModel->getBySlug($slug);

        // Verify page exists and is published
        if (!$page || $page->status !== 'published') {
            $this->view('error/404', ['title' => 'Halaman Tidak Ditemukan']);
            exit;
        }

        $this->view('frontend/page', [
            'title' => $page->title . ' - NexusCMS',
            'page' => $page
        ]);
    }

    public function category()
    {
        $slug = trim($_GET['slug'] ?? '');
        
        if (empty($slug)) {
            $this->view('error/404', ['title' => 'Kategori Tidak Ditemukan']);
            exit;
        }

        $categoryModel = new Category();
        $category = $categoryModel->getBySlug($slug);

        if (!$category) {
            $this->view('error/404', ['title' => 'Kategori Tidak Ditemukan']);
            exit;
        }

        $articles = $this->articleModel->getPublishedByCategory($category->id);

        $this->view('frontend/category', [
            'title' => 'Kategori: ' . $category->name . ' - NexusCMS',
            'category' => $category,
            'articles' => $articles
        ]);
    }
}