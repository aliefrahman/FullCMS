<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Helpers\Session;
use App\Helpers\Auth;

class CategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        // Only roles with 'publish_articles' permission can manage categories (Admin & Editor)
        Auth::requirePermission('publish_articles');
        
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $categories = $this->categoryModel->getAllWithCount();

        $data = [
            'categories' => $categories,
            'title' => 'Kategori Artikel',
            'activePage' => 'categories',
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/categories/index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        $name = \App\Helpers\Security::sanitize(trim($_POST['name'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $description = \App\Helpers\Security::sanitize(trim($_POST['description'] ?? ''));

        if (empty($name)) {
            Session::flash('error', 'Nama kategori wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        // Generate slug if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Check if slug exists
        if ($this->categoryModel->checkSlugExists($slug)) {
            Session::flash('error', 'Kategori dengan slug tersebut sudah ada.');
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        $saveData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];

        if ($this->categoryModel->create($saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('CATEGORY_CREATED', 'Category created: ' . $name);
            Session::flash('success', 'Kategori berhasil ditambahkan.');
        } else {
            Session::flash('error', 'Gagal menambahkan kategori.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/categories');
        exit;
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $name = \App\Helpers\Security::sanitize(trim($_POST['name'] ?? ''));
        $slug = \App\Helpers\Security::sanitize(trim($_POST['slug'] ?? ''));
        $description = \App\Helpers\Security::sanitize(trim($_POST['description'] ?? ''));

        if (empty($name) || $id === 0) {
            Session::flash('error', 'Nama kategori wajib diisi.');
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Check unique slug excluding self
        if ($this->categoryModel->checkSlugExists($slug, $id)) {
            Session::flash('error', 'Slug sudah digunakan oleh kategori lain.');
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        $saveData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ];

        if ($this->categoryModel->update($id, $saveData)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('CATEGORY_UPDATED', 'Category updated ID: ' . $id . ' (' . $name . ')');
            Session::flash('success', 'Kategori berhasil diperbarui.');
        } else {
            Session::flash('error', 'Gagal memperbarui kategori.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/categories');
        exit;
    }

    public function delete()
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id === 0) {
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        // Check if category has articles
        if ($this->categoryModel->hasArticles($id)) {
            Session::flash('error', 'Kategori tidak dapat dihapus karena masih memiliki artikel terkait. Kosongkan kategori terlebih dahulu.');
            header('Location: ' . PUBLIC_URL . '/admin/categories');
            exit;
        }

        if ($this->categoryModel->delete($id)) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('CATEGORY_DELETED', 'Category deleted ID: ' . $id);
            Session::flash('success', 'Kategori berhasil dihapus.');
        } else {
            Session::flash('error', 'Gagal menghapus kategori.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/categories');
        exit;
    }
}
