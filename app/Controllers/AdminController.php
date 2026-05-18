<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Core\Database;

class AdminController extends Controller
{
    protected $currentUser;

    public function __construct()
    {
        // Enforce active login session
        if (!Session::has('user_id')) {
            Session::flash('error', 'Silakan masuk terlebih dahulu untuk mengakses panel admin.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Get and validate current user from database (prevents stale/deleted session crashes)
        $db = new Database();
        $db->query("SELECT * FROM users WHERE id = :id");
        $db->bind(':id', Session::get('user_id'));
        $user = $db->single();

        if (!$user) {
            // Destroy stale session if user is not found in database anymore
            Session::destroy();
            Session::init();
            Session::flash('error', 'Akun Anda tidak ditemukan di sistem. Silakan masuk kembali.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        if ($user->status !== 'active') {
            // Log out user if deactivated by admin
            Session::destroy();
            Session::init();
            Session::flash('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        $this->currentUser = $user;

        // Auto-sync session values with latest DB state in case of updates
        Session::set('username', $user->username);
        Session::set('full_name', $user->full_name);
        Session::set('email', $user->email);
        Session::set('role', $user->role);
        Session::set('avatar', $user->avatar);

        // Enforce role-based access authorization control using Auth helper
        Auth::requirePermission('access_admin');
    }

    /**
     * Render the Admin Control Panel Dashboard
     */
    public function dashboard()
    {
        $db = new Database();

        // Query real-time metrics from cms_db.users table
        $db->query("SELECT COUNT(*) as total FROM users");
        $totalUsers = $db->single()->total;

        $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
        $totalAdmins = $db->single()->total;

        $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'subscriber'");
        $totalSubscribers = $db->single()->total;

        $db->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
        $activeUsers = $db->single()->total;

        // Fetch recent 5 registered users
        $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        $recentUsers = $db->resultSet();

        // subscriber-specific stats
        $myArticlesCount = 0;
        $myCommentsCount = 0;
        $myArticles = [];
        $myComments = [];
        $currentUserId = Session::get('user_id');

        if (Session::get('role') === 'subscriber' || Session::get('role') === 'author') {
            // Count my articles
            $db->query("SELECT COUNT(*) as total FROM articles WHERE author_id = :author_id");
            $db->bind(':author_id', $currentUserId);
            $myArticlesCount = $db->single()->total;

            // Count my comments
            $db->query("SELECT COUNT(*) as total FROM comments WHERE user_id = :user_id");
            $db->bind(':user_id', $currentUserId);
            $myCommentsCount = $db->single()->total;

            // Fetch my recent articles
            $db->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.author_id = :author_id ORDER BY a.created_at DESC LIMIT 5");
            $db->bind(':author_id', $currentUserId);
            $myArticles = $db->resultSet();

            // Fetch my recent comments
            $db->query("SELECT c.*, a.title as article_title, a.slug as article_slug FROM comments c JOIN articles a ON c.article_id = a.id WHERE c.user_id = :user_id ORDER BY c.created_at DESC LIMIT 5");
            $db->bind(':user_id', $currentUserId);
            $myComments = $db->resultSet();
        }

        $data = [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalSubscribers' => $totalSubscribers,
            'activeUsers' => $activeUsers,
            'recentUsers' => $recentUsers,
            'myArticlesCount' => $myArticlesCount,
            'myCommentsCount' => $myCommentsCount,
            'myArticles' => $myArticles,
            'myComments' => $myComments,
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/dashboard', $data);
    }

    /**
     * User Management: List all users
     */
    public function users()
    {
        Auth::requirePermission('manage_users');

        $db = new Database();
        $db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $db->resultSet();

        $data = [
            'users' => $users,
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/users/index', $data);
    }

    /**
     * User Management: Create User
     */
    public function createUser()
    {
        Auth::requirePermission('manage_users');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        $db = new Database();
        
        $fullName = \App\Helpers\Security::sanitize(trim($_POST['full_name'] ?? ''));
        $username = \App\Helpers\Security::sanitize(trim($_POST['username'] ?? ''));
        $email = \App\Helpers\Security::sanitize(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = \App\Helpers\Security::sanitize(trim($_POST['role'] ?? 'subscriber'));
        $status = \App\Helpers\Security::sanitize(trim($_POST['status'] ?? 'active'));

        if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
            Session::flash('error', 'Semua kolom wajib diisi untuk menambahkan user!');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        if (!\App\Helpers\Security::validateEmail($email)) {
            Session::flash('error', 'Format email tidak valid.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        if (!\App\Helpers\Security::validateUsername($username)) {
            Session::flash('error', 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_), sepanjang 3-20 karakter.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // 9. Password Policy Enforcement
        $passwordError = \App\Helpers\Security::validatePasswordStrength($password);
        if ($passwordError !== true) {
            Session::flash('error', $passwordError);
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // Check unique constraints
        $db->query("SELECT id FROM users WHERE email = :email");
        $db->bind(':email', $email);
        if ($db->single()) {
            Session::flash('error', 'Alamat email sudah digunakan.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        $db->query("SELECT id FROM users WHERE username = :username");
        $db->bind(':username', $username);
        if ($db->single()) {
            Session::flash('error', 'Username sudah digunakan.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // Insert
        $db->query("INSERT INTO users (full_name, username, email, password, role, status) VALUES (:full_name, :username, :email, :password, :role, :status)");
        $db->bind(':full_name', $fullName);
        $db->bind(':username', $username);
        $db->bind(':email', $email);
        $db->bind(':password', password_hash($password, PASSWORD_BCRYPT));
        $db->bind(':role', $role);
        $db->bind(':status', $status);

        if ($db->execute()) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('USER_CREATED', 'Admin created user: ' . $username);
            Session::flash('success', 'User berhasil ditambahkan ke database!');
        } else {
            Session::flash('error', 'Terjadi kesalahan sistem.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/users');
        exit;
    }

    /**
     * User Management: Update User
     */
    public function updateUser()
    {
        Auth::requirePermission('manage_users');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        $db = new Database();
        
        $id = intval($_POST['id'] ?? 0);
        $fullName = \App\Helpers\Security::sanitize(trim($_POST['full_name'] ?? ''));
        $username = \App\Helpers\Security::sanitize(trim($_POST['username'] ?? ''));
        $email = \App\Helpers\Security::sanitize(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = \App\Helpers\Security::sanitize(trim($_POST['role'] ?? 'subscriber'));
        $status = \App\Helpers\Security::sanitize(trim($_POST['status'] ?? 'active'));

        if (empty($fullName) || empty($username) || empty($email) || $id === 0) {
            Session::flash('error', 'Semua kolom bertanda wajib harus diisi!');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        if (!\App\Helpers\Security::validateEmail($email)) {
            Session::flash('error', 'Format email tidak valid.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        if (!\App\Helpers\Security::validateUsername($username)) {
            Session::flash('error', 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_), sepanjang 3-20 karakter.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        if (!empty($password)) {
            // 9. Password Policy Enforcement
            $passwordError = \App\Helpers\Security::validatePasswordStrength($password);
            if ($passwordError !== true) {
                Session::flash('error', $passwordError);
                header('Location: ' . PUBLIC_URL . '/admin/users');
                exit;
            }
        }

        // Validate unique email excluding current ID
        $db->query("SELECT id FROM users WHERE email = :email AND id != :id");
        $db->bind(':email', $email);
        $db->bind(':id', $id);
        if ($db->single()) {
            Session::flash('error', 'Email sudah digunakan oleh user lain.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // Validate unique username excluding current ID
        $db->query("SELECT id FROM users WHERE username = :username AND id != :id");
        $db->bind(':username', $username);
        $db->bind(':id', $id);
        if ($db->single()) {
            Session::flash('error', 'Username sudah digunakan oleh user lain.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // Prepare query depending on password change
        if (!empty($password)) {
            $db->query("UPDATE users SET full_name = :full_name, username = :username, email = :email, password = :password, role = :role, status = :status WHERE id = :id");
            $db->bind(':password', password_hash($password, PASSWORD_BCRYPT));
        } else {
            $db->query("UPDATE users SET full_name = :full_name, username = :username, email = :email, role = :role, status = :status WHERE id = :id");
        }

        $db->bind(':full_name', $fullName);
        $db->bind(':username', $username);
        $db->bind(':email', $email);
        $db->bind(':role', $role);
        $db->bind(':status', $status);
        $db->bind(':id', $id);

        if ($db->execute()) {
            // Update session if editing self
            if ($id === intval(Session::get('user_id'))) {
                Session::set('username', $username);
                Session::set('full_name', $fullName);
                Session::set('email', $email);
                Session::set('role', $role);
            }
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('USER_UPDATED', 'Admin updated user ID: ' . $id . ' (' . $username . ')');
            Session::flash('success', 'User berhasil diperbarui!');
        } else {
            Session::flash('error', 'Gagal memperbarui user.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/users');
        exit;
    }

    /**
     * User Management: Delete User
     */
    public function deleteUser()
    {
        Auth::requirePermission('manage_users');

        $id = intval($_GET['id'] ?? 0);
        $currentUserId = intval(Session::get('user_id'));

        if ($id === 0) {
            Session::flash('error', 'ID user tidak ditemukan.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        // Prevent self-deletion
        if ($id === $currentUserId) {
            Session::flash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
            header('Location: ' . PUBLIC_URL . '/admin/users');
            exit;
        }

        $db = new Database();
        $db->query("DELETE FROM users WHERE id = :id");
        $db->bind(':id', $id);

        if ($db->execute()) {
            // 8. Audit Logging
            \App\Helpers\Security::logAudit('USER_DELETED', 'Admin deleted user ID: ' . $id);
            Session::flash('success', 'User berhasil dihapus dari sistem database.');
        } else {
            Session::flash('error', 'Gagal menghapus user.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/users');
        exit;
    }

    /**
     * Access/Role Management: Display Matrix
     */
    public function roles()
    {
        Auth::requirePermission('manage_roles');

        $db = new Database();

        // Get counts grouped by role
        $db->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
        $roleCountsRaw = $db->resultSet();

        $roleCounts = [
            'admin' => 0,
            'editor' => 0,
            'author' => 0,
            'subscriber' => 0
        ];
        foreach ($roleCountsRaw as $r) {
            $roleCounts[$r->role] = $r->total;
        }

        // Fetch dynamic permissions structure from centralized Auth config
        $permissionsMap = Auth::getRolePermissions();

        // High fidelity roles description and permission sets
        $rolesData = [
            'admin' => [
                'name' => 'Administrator',
                'description' => 'Akses penuh dan tidak terbatas ke seluruh sistem, analitik, pengaturan, dan manajemen user.',
                'color' => 'bg-rose-50 border-rose-100 text-rose-600',
                'count' => $roleCounts['admin'],
                'permissions' => $permissionsMap['admin']
            ],
            'editor' => [
                'name' => 'Editor',
                'description' => 'Akses moderasi konten penuh. Dapat mempublikasikan, mengedit, dan menghapus artikel buatan sendiri maupun milik orang lain.',
                'color' => 'bg-indigo-50 border-indigo-100 text-indigo-600',
                'count' => $roleCounts['editor'],
                'permissions' => $permissionsMap['editor']
            ],
            'author' => [
                'name' => 'Author',
                'description' => 'Akses kontributor. Dapat membuat dan mengedit artikel draf sendiri, namun tidak dapat mempublikasikannya tanpa persetujuan editor/admin.',
                'color' => 'bg-amber-50 border-amber-100 text-amber-600',
                'count' => $roleCounts['author'],
                'permissions' => $permissionsMap['author']
            ],
            'subscriber' => [
                'name' => 'Subscriber',
                'description' => 'Akses pengguna umum (Pembaca). Hanya dapat membaca artikel terpublikasi dan memberikan komentar jika diaktifkan.',
                'color' => 'bg-slate-100 border-slate-200 text-slate-650',
                'count' => $roleCounts['subscriber'],
                'permissions' => $permissionsMap['subscriber']
            ]
        ];

        $data = [
            'rolesData' => $rolesData,
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/roles/index', $data);
    }

    /**
     * Access/Role Management: Update Simulation
     */
    public function updateRoles()
    {
        Auth::requirePermission('manage_roles');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rolesKeys = ['subscriber', 'author', 'editor', 'admin'];
            $finalPermissions = [];
            
            // Loop through defined roles and safely extract posted permissions array
            foreach ($rolesKeys as $rKey) {
                $finalPermissions[$rKey] = isset($_POST['permissions'][$rKey]) && is_array($_POST['permissions'][$rKey]) 
                    ? $_POST['permissions'][$rKey] 
                    : [];
            }

            // Implicit dependencies: Ensure admin ALWAYS has 'all_access' and 'access_admin'
            if (!in_array('all_access', $finalPermissions['admin'])) {
                $finalPermissions['admin'][] = 'all_access';
            }
            if (!in_array('access_admin', $finalPermissions['admin'])) {
                $finalPermissions['admin'][] = 'access_admin';
            }

            // Implicit dependencies: If a role has any backend capabilities, auto-grant 'access_admin'
            $backendPermissions = ['manage_users', 'manage_roles', 'publish_articles', 'edit_articles', 'create_articles'];
            foreach ($finalPermissions as $rKey => $perms) {
                if (count(array_intersect($perms, $backendPermissions)) > 0) {
                    if (!in_array('access_admin', $perms)) {
                        $finalPermissions[$rKey][] = 'access_admin';
                    }
                }
            }

            // Attempt to save to JSON file permanently
            $configFile = __DIR__ . '/../../config/permissions.json';
            $jsonContent = json_encode($finalPermissions, JSON_PRETTY_PRINT);
            
            if (file_put_contents($configFile, $jsonContent) !== false) {
                Session::flash('success', 'Konfigurasi Hak Akses (Role Permissions Matrix) berhasil disimpan permanen dan diterapkan ke sistem!');
            } else {
                Session::flash('error', 'Gagal menyimpan konfigurasi. Pastikan folder config/ memiliki izin tulis (writable) oleh server.');
            }
        }

        header('Location: ' . PUBLIC_URL . '/admin/roles');
        exit;
    }

    /**
     * User Profile: Render profile management screen
     */
    public function profile()
    {
        $data = [
            'user' => $this->currentUser,
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/profile', $data);
    }

    /**
     * User Profile: Process profile info updates
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        $db = new Database();
        $id = Session::get('user_id');
        
        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($fullName) || empty($username) || empty($email)) {
            Session::flash('error', 'Semua kolom profil wajib diisi!');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        // Validate unique email excluding current ID
        $db->query("SELECT id FROM users WHERE email = :email AND id != :id");
        $db->bind(':email', $email);
        $db->bind(':id', $id);
        if ($db->single()) {
            Session::flash('error', 'Email sudah digunakan oleh user lain.');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        // Validate unique username excluding current ID
        $db->query("SELECT id FROM users WHERE username = :username AND id != :id");
        $db->bind(':username', $username);
        $db->bind(':id', $id);
        if ($db->single()) {
            Session::flash('error', 'Username sudah digunakan oleh user lain.');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        $db->query("UPDATE users SET full_name = :full_name, username = :username, email = :email WHERE id = :id");
        $db->bind(':full_name', $fullName);
        $db->bind(':username', $username);
        $db->bind(':email', $email);
        $db->bind(':id', $id);

        if ($db->execute()) {
            // Instantly update active sessions so changes propagate immediately
            Session::set('username', $username);
            Session::set('full_name', $fullName);
            Session::set('email', $email);
            
            Session::flash('success', 'Informasi profil Anda berhasil diperbarui!');
        } else {
            Session::flash('error', 'Gagal memperbarui profil.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/profile');
        exit;
    }

    /**
     * User Profile: Process secure password changes
     */
    public function updateProfilePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        $db = new Database();
        $id = Session::get('user_id');
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::flash('error', 'Semua kolom password wajib diisi!');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Konfirmasi kata sandi baru tidak cocok.');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        // 9. Password Policy Enforcement
        $passwordError = \App\Helpers\Security::validatePasswordStrength($newPassword);
        if ($passwordError !== true) {
            Session::flash('error', $passwordError);
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        // Verify current password first
        $db->query("SELECT password FROM users WHERE id = :id");
        $db->bind(':id', $id);
        $user = $db->single();

        if ($user && password_verify($currentPassword, $user->password)) {
            // Update
            $db->query("UPDATE users SET password = :password WHERE id = :id");
            $db->bind(':password', password_hash($newPassword, PASSWORD_BCRYPT));
            $db->bind(':id', $id);

            if ($db->execute()) {
                // 8. Audit Logging
                \App\Helpers\Security::logAudit('PASSWORD_UPDATE', 'User changed their password: ' . Session::get('username'));
                Session::flash('success', 'Kata sandi Anda berhasil diperbarui dengan aman!');
            } else {
                Session::flash('error', 'Gagal memperbarui kata sandi.');
            }
        } else {
            Session::flash('error', 'Kata sandi saat ini yang Anda masukkan salah.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/profile');
        exit;
    }

    /**
     * User Profile: Process secure avatar upload with high-fidelity GD compression
     */
    public function updateAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        $id = Session::get('user_id');
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Gagal mengunggah file. Silakan pilih file gambar yang valid.');
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        // 6. Secure Upload checking (validation & cryptographic naming)
        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
        $uploadResult = \App\Helpers\Security::secureUpload($_FILES['avatar'], ['jpg', 'jpeg', 'png', 'gif', 'webp'], 5242880, $uploadDir);
        if (!$uploadResult['success']) {
            Session::flash('error', 'Gagal mengunggah avatar: ' . $uploadResult['message']);
            header('Location: ' . PUBLIC_URL . '/admin/profile');
            exit;
        }

        $secureFilePath = $uploadResult['filepath'];

        // Generate unique filename for the compressed target (convert to .jpg for unified high-efficiency compression)
        $newFileName = 'avatar_' . $id . '_' . time() . '.jpg';
        $destPath = $uploadDir . $newFileName;

        // Perform high-efficiency resizing and compression
        if ($this->compressImage($secureFilePath, $destPath, 300, 300, 80)) {
            // Delete the securely uploaded temp raw file
            @unlink($secureFilePath);

            $db = new Database();
            
            // Get old avatar to delete it and clean up disk
            $db->query("SELECT avatar FROM users WHERE id = :id");
            $db->bind(':id', $id);
            $currentUser = $db->single();
            
            if ($currentUser && !empty($currentUser->avatar)) {
                $oldAvatarPath = $uploadDir . $currentUser->avatar;
                if (file_exists($oldAvatarPath)) {
                    @unlink($oldAvatarPath);
                }
            }

            // Update database
            $db->query("UPDATE users SET avatar = :avatar WHERE id = :id");
            $db->bind(':avatar', $newFileName);
            $db->bind(':id', $id);

            if ($db->execute()) {
                // Update session and controller currentUser representation
                Session::set('avatar', $newFileName);
                if ($this->currentUser) {
                    $this->currentUser->avatar = $newFileName;
                }
                
                // 8. Audit Logging
                \App\Helpers\Security::logAudit('AVATAR_UPDATE', 'Avatar successfully updated for user ID: ' . $id);

                Session::flash('success', 'Avatar berhasil diperbarui dan dikompresi dengan optimal!');
            } else {
                Session::flash('error', 'Gagal memperbarui database avatar.');
            }
        } else {
            @unlink($secureFilePath);
            Session::flash('error', 'Gagal memproses dan mengompresi gambar.');
        }

        header('Location: ' . PUBLIC_URL . '/admin/profile');
        exit;
    }

    /**
     * Compress, crop, and resize images using the GD library
     */
    private function compressImage($sourcePath, $destinationPath, $targetWidth = 300, $targetHeight = 300, $quality = 80)
    {
        list($width, $height, $type) = getimagesize($sourcePath);
        
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($sourcePath);
                if ($srcImage) {
                    imagealphablending($srcImage, true);
                    imagesavealpha($srcImage, true);
                }
                break;
            case IMAGETYPE_GIF:
                $srcImage = @imagecreatefromgif($sourcePath);
                break;
            case 18: // IMAGETYPE_WEBP
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = @imagecreatefromwebp($sourcePath);
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }

        if (!$srcImage) {
            return false;
        }

        // Create blank truecolor canvas
        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if (!$dstImage) {
            return false;
        }

        // Fill background with white for transparency conversions (PNG/GIF to JPEG)
        $white = imagecolorallocate($dstImage, 255, 255, 255);
        imagefill($dstImage, 0, 0, $white);

        // Center-crop calculations
        $srcRatio = $width / $height;
        $dstRatio = $targetWidth / $targetHeight;

        if ($srcRatio >= $dstRatio) {
            // Source is wider than target square: keep height, crop width sides
            $newHeight = $height;
            $newWidth = $height * $dstRatio;
            $srcX = ($width - $newWidth) / 2;
            $srcY = 0;
        } else {
            // Source is taller than target square: keep width, crop height sides
            $newWidth = $width;
            $newHeight = $width / $dstRatio;
            $srcX = 0;
            $srcY = ($height - $newHeight) / 2;
        }

        // Perform smart high-fidelity resampling
        imagecopyresampled($dstImage, $srcImage, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $newWidth, $newHeight);

        // Save as optimized JPEG
        $result = imagejpeg($dstImage, $destinationPath, $quality);

        return $result;
    }

    /**
     * Display Comments Moderation List
     */
    public function comments()
    {
        // Enforce that only 'admin' role can moderate comments
        if (Session::get('role') !== 'admin') {
            Session::flash('error', 'Hanya administrator yang memiliki akses ke moderasi komentar.');
            header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            exit;
        }

        $commentModel = new \App\Models\Comment();
        $comments = $commentModel->getAll();

        $data = [
            'title' => 'Moderasi Komentar - NexusCMS',
            'activePage' => 'comments',
            'comments' => $comments,
            'username' => Session::get('username'),
            'role' => Session::get('role'),
            'fullName' => Session::get('full_name')
        ];

        $this->view('backend/comments/index', $data);
    }

    /**
     * Approve Comment
     */
    public function approveComment()
    {
        // Enforce that only 'admin' role can moderate comments
        if (Session::get('role') !== 'admin') {
            Session::flash('error', 'Hanya administrator yang memiliki akses ke moderasi komentar.');
            header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/comments');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $commentModel = new \App\Models\Comment();
            if ($commentModel->updateStatus($id, 'approved')) {
                \App\Helpers\Security::logAudit('COMMENT_APPROVED', 'Admin approved comment ID: ' . $id);
                Session::flash('success', 'Komentar berhasil disetujui!');
            } else {
                Session::flash('error', 'Gagal menyetujui komentar.');
            }
        }

        header('Location: ' . PUBLIC_URL . '/admin/comments');
        exit;
    }

    /**
     * Mark Comment as Spam
     */
    public function spamComment()
    {
        // Enforce that only 'admin' role can moderate comments
        if (Session::get('role') !== 'admin') {
            Session::flash('error', 'Hanya administrator yang memiliki akses ke moderasi komentar.');
            header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/admin/comments');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $commentModel = new \App\Models\Comment();
            if ($commentModel->updateStatus($id, 'spam')) {
                \App\Helpers\Security::logAudit('COMMENT_SPAM', 'Admin marked comment ID: ' . $id . ' as spam');
                Session::flash('success', 'Komentar berhasil ditandai sebagai spam!');
            } else {
                Session::flash('error', 'Gagal memproses komentar.');
            }
        }

        header('Location: ' . PUBLIC_URL . '/admin/comments');
        exit;
    }

    /**
     * Delete Comment
     */
    public function deleteComment()
    {
        // Enforce that only 'admin' role can moderate comments
        if (Session::get('role') !== 'admin') {
            Session::flash('error', 'Hanya administrator yang memiliki akses ke moderasi komentar.');
            header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            exit;
        }

        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $commentModel = new \App\Models\Comment();
            if ($commentModel->delete($id)) {
                \App\Helpers\Security::logAudit('COMMENT_DELETED', 'Admin deleted comment ID: ' . $id);
                Session::flash('success', 'Komentar berhasil dihapus secara permanen!');
            } else {
                Session::flash('error', 'Gagal menghapus komentar.');
            }
        }

        header('Location: ' . PUBLIC_URL . '/admin/comments');
        exit;
    }
}
