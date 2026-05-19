<?php
namespace App\Helpers;

use App\Helpers\Session;

class Security
{
    /**
     * 1. CSRF Protection: Generate secure CSRF Token
     */
    public static function csrfToken()
    {
        Session::init();
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    /**
     * 1. CSRF Protection: Generate HTML Hidden CSRF Input
     */
    public static function csrfField()
    {
        $token = self::csrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * 1. CSRF Protection: Validate incoming POST/PUT/DELETE CSRF request
     */
    public static function validateCsrf()
    {
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
            Session::init();
            $sessionToken = Session::get('csrf_token');
            $postToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!$sessionToken || !hash_equals($sessionToken, (string)$postToken)) {
                http_response_code(403);
                self::logAudit('CSRF_FAILURE', 'CSRF token mismatch or missing. IP: ' . self::getIpAddress());

                echo "<div style='font-family: system-ui, sans-serif; text-align: center; padding: 100px 20px; background-color: #f8fafc; min-height: 100vh; box-sizing: border-box;'>";
                echo "<div style='max-width: 550px; margin: 0 auto; background: white; padding: 45px; border-radius: 28px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;'>";
                echo "<div style='width: 64px; height: 64px; background: #fff1f2; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;'>";
                echo "<svg style='width: 32px; height: 32px; color: #f43f5e;' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/></svg>";
                echo "</div>";
                echo "<h1 style='color: #0f172a; margin: 0 0 12px; font-size: 24px; font-weight: 850; letter-spacing: -0.025em;'>403 Verifikasi CSRF Gagal</h1>";
                echo "<p style='color: #475569; margin: 0 0 32px; font-size: 14.5px; line-height: 1.6;'>Permintaan tidak dapat diproses karena token keamanan tidak valid atau sudah kedaluwarsa demi melindungi sesi Anda.</p>";
                echo "<button onclick='window.history.back()' style='background: #0f172a; color: white; border: none; padding: 14px 28px; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; width: 100%; transition: all 0.2s;'>Kembali ke Halaman Sebelumnya</button>";
                echo "</div>";
                echo "</div>";
                exit;
            }
        }
    }

    /**
     * 2. Session Hardening: Set secure options and validate sessions
     */
    public static function hardenSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_only_cookies', 1);
            ini_set('session.use_trans_sid', 0);
            ini_set('session.cookie_httponly', 1);

            $isHttps = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1))
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            if ($isHttps) {
                ini_set('session.cookie_secure', 1);
            }

            if (PHP_VERSION_ID >= 70300) {
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => '',
                    'secure' => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                session_set_cookie_params(0, '/; SameSite=Lax', '', $isHttps, true);
            }

            session_start();
        }

        // Validate user fingerprint to prevent session fixation / hijacking
        $fingerprint = md5(($_SERVER['HTTP_USER_AGENT'] ?? 'unknown_ua') . '_' . self::getIpSubnet());
        if (!isset($_SESSION['_fingerprint'])) {
            $_SESSION['_fingerprint'] = $fingerprint;
        } elseif ($_SESSION['_fingerprint'] !== $fingerprint) {
            self::logAudit('SESSION_HIJACK_ATTEMPT', 'Session fingerprint mismatch. Revoking session.');
            
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            session_start();
            $_SESSION['_fingerprint'] = $fingerprint;
        }

        // Regenerate session ID periodically (every 30 mins)
        if (!isset($_SESSION['_created_time'])) {
            $_SESSION['_created_time'] = time();
        } elseif (time() - $_SESSION['_created_time'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created_time'] = time();
        }
    }

    /**
     * Helper to get client IP
     */
    public static function getIpAddress()
    {
        return $_SERVER['HTTP_CLIENT_IP'] 
            ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? '127.0.0.1';
    }

    /**
     * Helper to get IP Subnet for fingerprint
     */
    private static function getIpSubnet()
    {
        $ip = self::getIpAddress();
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.0';
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            return $parts[0] . ':' . $parts[1] . ':' . $parts[2] . ':' . $parts[3] . '::0';
        }
        return 'unknown';
    }

    /**
     * 3. Input Validation & Sanitization: Sanitize string inputs
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        if ($input === null) return null;
        return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateUsername($username)
    {
        return preg_match('/^[a-zA-Z0-9_\-]{3,20}$/', $username);
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 4. Output Encoding: Double XSS Prevention Encoder
     */
    public static function escape($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'escape'], $value);
        }
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * 5. Rate Limiting: Lockout brute force attempts
     */
    public static function checkRateLimit($username)
    {
        $ip = self::getIpAddress();
        $db = new \App\Core\Database();

        // Flush expired records (older than 15 minutes / 900 seconds)
        $expiry = time() - 900;
        $db->query("DELETE FROM login_attempts WHERE attempt_time < :expiry");
        $db->bind(':expiry', $expiry);
        $db->execute();

        // Check fail counts
        $db->query("SELECT COUNT(*) as total FROM login_attempts WHERE ip_address = :ip OR username = :username");
        $db->bind(':ip', $ip);
        $db->bind(':username', $username);
        $res = $db->single();

        if ($res && $res->total >= 5) {
            return false; // Locked out!
        }
        return true;
    }

    public static function registerLoginFailure($username)
    {
        $ip = self::getIpAddress();
        $db = new \App\Core\Database();
        $db->query("INSERT INTO login_attempts (ip_address, username, attempt_time) VALUES (:ip, :username, :time)");
        $db->bind(':ip', $ip);
        $db->bind(':username', $username);
        $db->bind(':time', time());
        $db->execute();

        self::logAudit('LOGIN_FAILURE', "Failed login attempt for username: $username");
    }

    public static function clearLoginAttempts($username)
    {
        $ip = self::getIpAddress();
        $db = new \App\Core\Database();
        $db->query("DELETE FROM login_attempts WHERE ip_address = :ip OR username = :username");
        $db->bind(':ip', $ip);
        $db->bind(':username', $username);
        $db->execute();
    }

    /**
     * 6. Secure File Upload: Deep inspection & cryptographically secure random names
     */
    public static function secureUpload($file, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 5242880, $destination = '/var/www/html/fullcms/public/uploads/articles/')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error atau file tidak terpilih.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file melampaui batas maksimum (' . ($maxSize / 1024 / 1024) . ' MB).'];
        }

        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Format file .' . $ext . ' tidak didukung.'];
        }

        // Validate content-type bytes securely
        $tmpPath = $file['tmp_name'];
        if (!file_exists($tmpPath) || !is_readable($tmpPath)) {
            return ['success' => false, 'message' => 'File tidak valid di server.'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath);

        $validMimes = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp']
        ];

        $matched = false;
        foreach ($validMimes as $validMime => $exts) {
            if ($mime === $validMime && in_array($ext, $exts)) {
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            return ['success' => false, 'message' => 'Konten file tidak cocok dengan ekstensinya (indikasi ancaman keamanan).'];
        }

        // Cryptographically secure random name to avoid directory traversal / shell injections
        $newFilename = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $targetPath = rtrim($destination, '/') . '/' . $newFilename;

        if (move_uploaded_file($tmpPath, $targetPath)) {
            return ['success' => true, 'filename' => $newFilename, 'filepath' => $targetPath];
        }

        return ['success' => false, 'message' => 'Internal Server Error dalam pemindahan upload.'];
    }

    /**
     * 7. Object-Level Authorization: Resource owner verification
     */
    public static function canModifyArticle($articleAuthorId)
    {
        Session::init();
        $currentUserId = Session::get('user_id');
        $role = Session::get('role');

        if ($role === 'admin' || $role === 'editor') {
            return true;
        }

        return (int)$currentUserId === (int)$articleAuthorId;
    }

    public static function canModifyUser($userIdToModify)
    {
        Session::init();
        $currentUserId = Session::get('user_id');
        $role = Session::get('role');

        if ($role === 'admin') {
            return true;
        }

        return (int)$currentUserId === (int)$userIdToModify;
    }

    private static $isLoggingAudit = false;

    /**
     * 8. Audit Logging: Safe DB Audit insertion
     */
    public static function logAudit($action, $details = null)
    {
        if (self::$isLoggingAudit) {
            return;
        }
        self::$isLoggingAudit = true;

        Session::init();
        $userId = Session::get('user_id');
        $username = Session::get('username');
        $ip = self::getIpAddress();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        try {
            $db = new \App\Core\Database();
            $db->query("INSERT INTO audit_logs (user_id, username, action, details, ip_address, user_agent) VALUES (:user_id, :username, :action, :details, :ip, :user_agent)");
            $db->bind(':user_id', $userId);
            $db->bind(':username', $username);
            $db->bind(':action', $action);
            $db->bind(':details', $details);
            $db->bind(':ip', $ip);
            $db->bind(':user_agent', $userAgent);
            $db->execute();
        } catch (\Exception $e) {
            error_log("Audit logging failure: " . $e->getMessage());
        }

        self::$isLoggingAudit = false;
    }

    /**
     * 9. Password Policy Enforcement: Checks strength requirements
     */
    public static function validatePasswordStrength($password)
    {
        if (strlen($password) < 8) {
            return 'Kata sandi harus minimal memiliki panjang 8 karakter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Kata sandi harus mengandung minimal satu huruf besar (A-Z).';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Kata sandi harus mengandung minimal satu huruf kecil (a-z).';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Kata sandi harus mengandung minimal satu angka (0-9).';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Kata sandi harus mengandung minimal satu karakter khusus (misal: @, #, $, %, !, ?, &, *).';
        }
        return true;
    }
}
