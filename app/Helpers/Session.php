<?php
namespace App\Helpers;

class Session
{
    public static function init()
    {
        \App\Helpers\Security::hardenSession();
    }

    /**
     * Set session key/value pair
     */
    public static function set($key, $value)
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
     * Get value of session key
     */
    public static function get($key)
    {
        self::init();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Check if session has a key
     */
    public static function has($key)
    {
        self::init();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete session key
     */
    public static function delete($key)
    {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Set or get a flash message
     */
    public static function flash($key, $message = null)
    {
        self::init();
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
        } else {
            $msg = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
    }

    /**
     * Check if a flash message exists
     */
    public static function hasFlash($key)
    {
        self::init();
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Destroy session completely (for logout)
     */
    public static function destroy()
    {
        self::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
