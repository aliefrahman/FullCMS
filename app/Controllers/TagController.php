<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tag;
use App\Helpers\Session;
use App\Helpers\Auth;

class TagController extends Controller
{
    private $tagModel;

    public function __construct()
    {
        // Require create_articles permission (Authors & Subscribers can access)
        Auth::requirePermission('create_articles');
        
        $this->tagModel = new Tag();
    }

    public function index()
    {
        // Index requires publish_articles (Editor & Admin only)
        Auth::requirePermission('publish_articles');

        $tags = $this->tagModel->getAllWithCount();

        $data = [
            'tags' => $tags,
            'title' => 'Tag Artikel',
            'activePage' => 'tags',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/tags/index', $data);
    }

    public function create()
    {
        // Anyone with create_articles can create tags
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        $name = \App\Helpers\Security::sanitize(trim($_POST['name'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));

        if (empty($name)) {
            Session::flash('error', 'Nama tag wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Check if slug exists
        if ($this->tagModel->checkSlugExists($slug)) {
            Session::flash('error', 'Tag dengan slug tersebut sudah ada.');
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        $saveData = [
            'name' => $name,
            'slug' => $slug
        ];

        if ($this->tagModel->create($saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('TAG_CREATED', 'Tag created: ' . $name);
            Session::flash('success', 'Tag berhasil ditambahkan.');
        } else {
            Session::flash('error', 'Gagal menambahkan tag.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/tags');
        exit;
    }

    /**
     * AJAX Endpoint to create tag on the fly
     * Allowed for anyone who has create_articles (including Authors/Subscribers)
     */
    public function ajaxCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak valid.']);
            exit;
        }

        $name = \App\Helpers\Security::sanitize(trim($_POST['name'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));

        if (empty($name)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Nama tag wajib diisi.']);
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Check if slug exists
        if ($this->tagModel->checkSlugExists($slug)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Tag dengan slug tersebut sudah ada.']);
            exit;
        }

        $saveData = [
            'name' => $name,
            'slug' => $slug
        ];

        if ($this->tagModel->create($saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('TAG_CREATED_AJAX', 'Tag created via AJAX: ' . $name);
            $newTag = $this->tagModel->getBySlug($slug);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Tag berhasil ditambahkan.',
                'tag' => [
                    'id' => $newTag->id,
                    'name' => $newTag->name,
                    'slug' => $newTag->slug
                ]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan tag baru ke database.']);
        }
        exit;
    }

    public function update()
    {
        // Update requires publish_articles (Editor & Admin only)
        Auth::requirePermission('publish_articles');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $name = \App\Helpers\Security::sanitize(trim($_POST['name'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));

        if (empty($name) || $id === 0) {
            Session::flash('error', 'Nama tag wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Check unique slug excluding self
        if ($this->tagModel->checkSlugExists($slug, $id)) {
            Session::flash('error', 'Slug sudah digunakan oleh tag lain.');
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        $saveData = [
            'name' => $name,
            'slug' => $slug
        ];

        if ($this->tagModel->update($id, $saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('TAG_UPDATED', 'Tag updated ID: ' . $id . ' (' . $name . ')');
            Session::flash('success', 'Tag berhasil diperbarui.');
        } else {
            Session::flash('error', 'Gagal memperbarui tag.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/tags');
        exit;
    }

    public function delete()
    {
        // Delete requires publish_articles (Editor & Admin only)
        Auth::requirePermission('publish_articles');

        $id = intval($_GET['id'] ?? 0);
        if ($id === 0) {
            header('Location: ' . PUBLIC_URL . '/admin/tags');
            exit;
        }

        if ($this->tagModel->delete($id)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('TAG_DELETED', 'Tag deleted ID: ' . $id);
            Session::flash('success', 'Tag berhasil dihapus.');
        } else {
            Session::flash('error', 'Gagal menghapus tag.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/tags');
        exit;
    }
}
