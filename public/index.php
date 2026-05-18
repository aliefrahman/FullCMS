<?php
require_once __DIR__ . '/../config/config.php';

// Menentukan base URL secara dinamis agar aset tetap bisa dimuat dari mana saja
$publicUrl = dirname($_SERVER['SCRIPT_NAME']);
if ($publicUrl === '/' || $publicUrl === '\\') {
    $publicUrl = '';
}
define('PUBLIC_URL', $publicUrl);

// Simple Autoloader untuk namespace App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize hardened sessions & check CSRF tokens dynamically for state-changing requests
App\Helpers\Session::init();
App\Helpers\Security::validateCsrf();

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CategoryController;
use App\Controllers\TagController;
use App\Controllers\ArticleController;
use App\Controllers\PageController;
use App\Controllers\CommentController;

// Inisialisasi Router
$router = new Router();

// Mendaftarkan routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/read', [HomeController::class, 'read']);
$router->get('/p', [HomeController::class, 'page']);
$router->get('/category', [HomeController::class, 'category']);

// Rute Otentikasi
$router->get('/auth', [AuthController::class, 'index']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->get('/auth/logout', [AuthController::class, 'logout']);

// Rute Admin Control Panel
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);

// Rute Manajemen User
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/create', [AdminController::class, 'createUser']);
$router->post('/admin/users/update', [AdminController::class, 'updateUser']);
$router->get('/admin/users/delete', [AdminController::class, 'deleteUser']);

// Rute Manajemen Hak Akses (Role)
$router->get('/admin/roles', [AdminController::class, 'roles']);
$router->post('/admin/roles/update', [AdminController::class, 'updateRoles']);

// Rute Profil User
$router->get('/admin/profile', [AdminController::class, 'profile']);
$router->post('/admin/profile/update', [AdminController::class, 'updateProfile']);
$router->post('/admin/profile/password', [AdminController::class, 'updateProfilePassword']);
$router->post('/admin/profile/update-avatar', [AdminController::class, 'updateAvatar']);

// Rute Kategori (Memerlukan izin publish_articles)
$router->get('/admin/categories', [CategoryController::class, 'index']);
$router->post('/admin/categories/create', [CategoryController::class, 'create']);
$router->post('/admin/categories/update', [CategoryController::class, 'update']);
$router->get('/admin/categories/delete', [CategoryController::class, 'delete']);

// Rute Tag (Memerlukan izin publish_articles)
$router->get('/admin/tags', [TagController::class, 'index']);
$router->post('/admin/tags/create', [TagController::class, 'create']);
$router->post('/admin/tags/ajax-create', [TagController::class, 'ajaxCreate']);
$router->post('/admin/tags/update', [TagController::class, 'update']);
$router->get('/admin/tags/delete', [TagController::class, 'delete']);

// Rute Artikel
$router->get('/admin/articles', [ArticleController::class, 'index']);
$router->get('/admin/articles/create', [ArticleController::class, 'create']);
$router->post('/admin/articles/store', [ArticleController::class, 'store']);
$router->get('/admin/articles/edit', [ArticleController::class, 'edit']);
$router->post('/admin/articles/update', [ArticleController::class, 'update']);
$router->get('/admin/articles/delete', [ArticleController::class, 'delete']);

// Rute Halaman Statis Admin
$router->get('/admin/pages', [PageController::class, 'index']);
$router->get('/admin/pages/create', [PageController::class, 'create']);
$router->post('/admin/pages/store', [PageController::class, 'store']);
$router->get('/admin/pages/edit', [PageController::class, 'edit']);
$router->post('/admin/pages/update', [PageController::class, 'update']);
$router->get('/admin/pages/delete', [PageController::class, 'delete']);

// Rute Komentar Frontend
$router->post('/comment/store', [CommentController::class, 'store']);

// Rute Moderasi Komentar Admin
$router->get('/admin/comments', [AdminController::class, 'comments']);
$router->post('/admin/comments/approve', [AdminController::class, 'approveComment']);
$router->post('/admin/comments/spam', [AdminController::class, 'spamComment']);
$router->get('/admin/comments/delete', [AdminController::class, 'deleteComment']);

$router->dispatch();