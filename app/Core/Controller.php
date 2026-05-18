<?php
namespace App\Core;

class Controller
{
    /**
     * Memuat file view dan mengirimkan data kepadanya
     *
     * @param string $view Nama file view di folder pages (tanpa .php)
     * @param array $data Array asosiatif berisi data untuk view
     */
    public function view($view, $data = [])
    {
        // Ekstrak data menjadi variabel
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // Get current response code; default to 404 if not set or OK (200)
            $code = http_response_code();
            if ($code === 200 || $code === false) {
                $code = 404;
            }
            
            http_response_code($code);
            
            $errorFile = __DIR__ . '/../../views/error/' . $code . '.php';
            if (file_exists($errorFile)) {
                require_once $errorFile;
            } else {
                echo "Error " . $code;
            }
        }
    }
}