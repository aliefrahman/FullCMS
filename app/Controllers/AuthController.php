<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\Session;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Render the Authentication Page (Login/Register Tabbed Form)
     */
    public function index()
    {
        // If already logged in, redirect based on roles
        if (Session::has('user_id')) {
            $role = Session::get('role');
            if (in_array($role, ['admin', 'editor', 'author'])) {
                header('Location: ' . PUBLIC_URL . '/admin/dashboard');
                exit;
            } else {
                header('Location: ' . PUBLIC_URL . '/');
                exit;
            }
        }

        $this->view('backend/auth/index');
    }

    /**
     * Handle User Login request
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Retrieve and sanitize credentials
        $identity = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identity) || empty($password)) {
            Session::flash('error', 'Semua kolom wajib diisi!');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // 5. Rate Limiting Check
        if (!\App\Helpers\Security::checkRateLimit($identity)) {
            Session::flash('error', 'Terlalu banyak percobaan masuk! Silakan coba lagi setelah 15 menit.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Attempt to find user by email or username
        $user = $this->userModel->findByEmail($identity);
        if (!$user) {
            $user = $this->userModel->findByUsername($identity);
        }

        // Verify credentials
        if ($user && password_verify($password, $user->password)) {
            // Check account status
            if ($user->status !== 'active') {
                \App\Helpers\Security::registerLoginFailure($identity);
                Session::flash('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
                header('Location: ' . PUBLIC_URL . '/auth');
                exit;
            }

            // 5. Rate Limiting: Clear failures on success
            \App\Helpers\Security::clearLoginAttempts($identity);

            // Set session variables
            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('email', $user->email);
            Session::set('full_name', $user->full_name);
            Session::set('role', $user->role);
            Session::set('avatar', $user->avatar);

            // 8. Audit Logging
            \App\Helpers\Security::logAudit('LOGIN_SUCCESS', 'User logged in successfully: ' . $user->username);

            Session::flash('success', 'Selamat datang kembali, ' . ($user->full_name ?? $user->username) . '!');

            // Role-Based Authorization Routing
            if (\App\Helpers\Auth::hasPermission('access_admin')) {
                // Redirect to backoffice administration panel dashboard
                header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            } else {
                // Redirect standard subscribers to public portal homepage
                header('Location: ' . PUBLIC_URL . '/');
            }
            exit;
        } else {
            // 5. Rate Limiting: Register failure
            \App\Helpers\Security::registerLoginFailure($identity);

            Session::flash('error', 'Email/Username atau password salah.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }
    }

    /**
     * Handle User Registration request
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Retrieve and sanitize input fields
        $fullName = \App\Helpers\Security::sanitize(trim($_POST['full_name'] ?? ''));
        $username = \App\Helpers\Security::sanitize(trim($_POST['username'] ?? ''));
        $email = \App\Helpers\Security::sanitize(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $requestedRole = 'subscriber'; // Force all public registrations to subscriber role for security

        // Validation checks
        if (empty($fullName) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            Session::flash('error', 'Semua kolom wajib diisi!');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'Konfirmasi kata sandi tidak cocok.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        if (!\App\Helpers\Security::validateEmail($email)) {
            Session::flash('error', 'Format email tidak valid.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        if (!\App\Helpers\Security::validateUsername($username)) {
            Session::flash('error', 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_), sepanjang 3-20 karakter.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // 9. Password Policy Enforcement
        $passwordError = \App\Helpers\Security::validatePasswordStrength($password);
        if ($passwordError !== true) {
            Session::flash('error', $passwordError);
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Check if email or username is already registered
        if ($this->userModel->findByEmail($email)) {
            Session::flash('error', 'Alamat email sudah terdaftar.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        if ($this->userModel->findByUsername($username)) {
            Session::flash('error', 'Username sudah digunakan oleh orang lain.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Perform User Registration
        $userData = [
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $requestedRole,
            'status' => 'active'
        ];

        if ($this->userModel->register($userData)) {
            // Retrieve the newly created user to log them in automatically
            $user = $this->userModel->findByEmail($email);

            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('email', $user->email);
            Session::set('full_name', $user->full_name);
            Session::set('role', $user->role);
            Session::set('avatar', $user->avatar);

            // 8. Audit Logging
            \App\Helpers\Security::logAudit('REGISTER_SUCCESS', 'User registered successfully: ' . $user->username);

            Session::flash('success', 'Registrasi berhasil! Selamat datang di FullCMS.');

            // Role-Based Authorization Routing
            if (in_array($user->role, ['admin', 'editor', 'author'])) {
                header('Location: ' . PUBLIC_URL . '/admin/dashboard');
            } else {
                header('Location: ' . PUBLIC_URL . '/');
            }
            exit;
        } else {
            Session::flash('error', 'Terjadi kesalahan sistem saat mendaftar. Silakan coba lagi.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }
    }

    /**
     * Handle User Logout request
     */
    public function logout()
    {
        // 8. Audit Logging
        \App\Helpers\Security::logAudit('LOGOUT', 'User logged out: ' . Session::get('username'));

        Session::destroy();
        Session::init();
        Session::flash('success', 'Anda telah berhasil keluar sistem.');
        header('Location: ' . PUBLIC_URL . '/auth');
        exit;
    }
}
