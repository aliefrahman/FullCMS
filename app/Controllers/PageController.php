<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Page;
use App\Helpers\Session;
use App\Helpers\Auth;

class PageController extends Controller
{
    private $pageModel;

    public function __construct()
    {
        // Enforce admin/editor authentication to manage static pages
        Auth::requirePermission('publish_articles');
        
        $this->pageModel = new Page();
    }

    /**
     * Show list of all static pages
     */
    public function index()
    {
        $pages = $this->pageModel->getAll();

        $data = [
            'pages' => $pages,
            'title' => 'Manajemen Halaman',
            'activePage' => 'pages',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/pages/index', $data);
    }

    /**
     * Show create static page form
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Halaman Baru',
            'activePage' => 'pages',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/pages/create', $data);
    }

    /**
     * Store a new static page
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/pages');
            exit;
        }

        $title = \App\Helpers\Security::sanitize(trim($_POST['title'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $content = trim($_POST['content'] ?? '');
        $metaDescription = \App\Helpers\Security::sanitize(trim($_POST['meta_description'] ?? ''));
        $status = \App\Helpers\Security::sanitize($_POST['status'] ?? 'published');

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Judul dan konten halaman wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/pages/create');
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        // Enforce unique slug
        if ($this->pageModel->checkSlugExists($slug)) {
            $slug = $slug . '-' . time();
        }

        $saveData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'meta_description' => $metaDescription,
            'status' => $status
        ];

        if ($this->pageModel->create($saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('PAGE_CREATED', 'Static page created: ' . $title);
            Session::flash('success', 'Halaman statis berhasil dibuat.');
            header('Location: ' . PUBLIC_URL . '/admin/pages');
        } else {
            Session::flash('error', 'Gagal membuat halaman statis.');
            header('Location: ' . PUBLIC_URL . '/admin/pages/create');
        }
        exit;
    }

    /**
     * Show edit static page form
     */
    public function edit()
    {
        $id = intval($_GET['id'] ?? 0);
        $page = $this->pageModel->getById($id);

        if (!$page) {
            Session::flash('error', 'Halaman tidak ditemukan.');
            header('Location: ' . PUBLIC_URL . '/admin/pages');
            exit;
        }

        $data = [
            'page' => $page,
            'title' => 'Edit Halaman: ' . $page->title,
            'activePage' => 'pages',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/pages/edit', $data);
    }

    /**
     * Update an existing static page
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/pages');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $title = \App\Helpers\Security::sanitize(trim($_POST['title'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $content = trim($_POST['content'] ?? '');
        $metaDescription = \App\Helpers\Security::sanitize(trim($_POST['meta_description'] ?? ''));
        $status = \App\Helpers\Security::sanitize($_POST['status'] ?? 'published');

        $page = $this->pageModel->getById($id);
        if (!$page) {
            Session::flash('error', 'Halaman tidak ditemukan.');
            header('Location: ' . PUBLIC_URL . '/admin/pages');
            exit;
        }

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Judul dan konten halaman wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/pages/edit?id=' . $id);
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        // Check unique slug excluding self
        if ($this->pageModel->checkSlugExists($slug, $id)) {
            Session::flash('error', 'Slug sudah digunakan oleh halaman lain.');
            header('Location: ' . PUBLIC_URL . '/admin/pages/edit?id=' . $id);
            exit;
        }

        $saveData = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'meta_description' => $metaDescription,
            'status' => $status
        ];

        if ($this->pageModel->update($id, $saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('PAGE_UPDATED', 'Static page updated ID: ' . $id . ' (' . $title . ')');
            Session::flash('success', 'Halaman statis berhasil diperbarui.');
            header('Location: ' . PUBLIC_URL . '/admin/pages');
        } else {
            Session::flash('error', 'Gagal memperbarui halaman statis.');
            header('Location: ' . PUBLIC_URL . '/admin/pages/edit?id=' . $id);
        }
        exit;
    }

    /**
     * Delete a static page
     */
    public function delete()
    {
        $id = intval($_GET['id'] ?? 0);
        $page = $this->pageModel->getById($id);

        if (!$page) {
            Session::flash('error', 'Halaman tidak ditemukan.');
            header('Location: ' . PUBLIC_URL . '/admin/pages');
            exit;
        }

        if ($this->pageModel->delete($id)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('PAGE_DELETED', 'Static page deleted ID: ' . $id . ' (' . $page->title . ')');
            Session::flash('success', 'Halaman statis berhasil dihapus.');
        } else {
            Session::flash('error', 'Gagal menghapus halaman statis.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/pages');
        exit;
    }
}
