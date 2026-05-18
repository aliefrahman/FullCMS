<?php
namespace App\Helpers;

use App\Helpers\Session;

class Auth
{
    /**
     * Define the matrix of permissions for each role.
     * This acts as the single source of truth for the system.
     */
    public static function getRolePermissions()
    {
        $configFile = __DIR__ . '/../../config/permissions.json';
        if (file_exists($configFile)) {
            $data = file_get_contents($configFile);
            $perms = json_decode($data, true);
            if (is_array($perms)) {
                return $perms;
            }
        }

        // Default fallback matrix if JSON configuration doesn't exist yet
        return [
            'admin' => [
                'all_access', 
                'access_admin', 
                'manage_users', 
                'manage_roles', 
                'publish_articles', 
                'edit_articles', 
                'create_articles', 
                'read_articles'
            ],
            'editor' => [
                'access_admin', 
                'publish_articles', 
                'edit_articles', 
                'create_articles', 
                'read_articles'
            ],
            'author' => [
                'access_admin', 
                'create_articles', 
                'read_articles'
            ],
            'subscriber' => [
                'read_articles'
            ]
        ];
    }

    /**
     * Check if the currently logged-in user has a specific permission.
     * @param string $permission
     * @return bool
     */
    public static function hasPermission($permission)
    {
        $role = Session::get('role') ?? 'subscriber';
        $permissions = self::getRolePermissions();

        // If role doesn't exist, fallback to subscriber
        $rolePerms = $permissions[$role] ?? $permissions['subscriber'];

        // Admin 'all_access' overrides everything
        if (in_array('all_access', $rolePerms)) {
            return true;
        }

        return in_array($permission, $rolePerms);
    }
    
    /**
     * Enforce a permission. If user does not have it, abort with 403 Forbidden.
     * @param string $permission
     */
    public static function requirePermission($permission)
    {
        if (!self::hasPermission($permission)) {
            http_response_code(403);
            
            $errorFile = __DIR__ . '/../../views/error/403.php';
            if (file_exists($errorFile)) {
                require_once $errorFile;
            } else {
                echo "<div style='font-family: system-ui, sans-serif; text-align: center; padding: 100px 20px; background-color: #f8fafc; min-height: 100vh; box-sizing: border-box;'>";
                echo "<div style='max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;'>";
                echo "<svg style='width: 64px; height: 64px; color: #f43f5e; margin: 0 auto 20px;' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/></svg>";
                echo "<h1 style='color: #0f172a; margin: 0 0 10px; font-size: 24px; font-weight: 800;'>403 Akses Ditolak</h1>";
                echo "<p style='color: #64748b; margin: 0 0 30px; font-size: 14px; line-height: 1.6;'>Anda tidak memiliki hak akses (permission: <strong style='color:#0f172a;'>{$permission}</strong>) untuk melihat halaman atau melakukan tindakan ini.</p>";
                echo "<button onclick='window.history.back()' style='background: #0f172a; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; cursor: pointer; width: 100%; transition: opacity 0.2s;' onmouseover='this.style.opacity=\"0.9\"' onmouseout='this.style.opacity=\"1\"'>Kembali ke Halaman Sebelumnya</button>";
                echo "</div>";
                echo "</div>";
            }
            exit;
        }
    }
}
