<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'CDP17s1850913#^_^');
define('DB_NAME', 'cms_db');

/**
 * 4. Output Encoding: Global HTML escaping helper (XSS prevention)
 */
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}