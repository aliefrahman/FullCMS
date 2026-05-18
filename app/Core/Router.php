<?php
namespace App\Core;
class Router
{
    protected $routes = [];

    public function add($route, $controller, $method = 'GET')
    {
        $this->routes[$method][$route] = $controller;
    }

    public function get($route, $controller)
    {
        $this->add($route, $controller, 'GET');
    }

    public function post($route, $controller)
    {
        $this->add($route, $controller, 'POST');
    }

    public function put($route, $controller)
    {
        $this->add($route, $controller, 'PUT');
    }

    public function delete($route, $controller)
    {
        $this->add($route, $controller, 'DELETE');
    }

    public function dispatch()
    {
        return $this->run();
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Menyesuaikan path URI agar router bisa berjalan walau berada di sub-folder
        $baseDir = dirname(dirname($_SERVER['SCRIPT_NAME']));
        if ($baseDir === '/' || $baseDir === '\\') {
            $baseDir = '';
        }

        if (!empty($baseDir) && strpos($uri, $baseDir) === 0) {
            $uri = substr($uri, strlen($baseDir));
        }

        // Hapus /public jika ada
        if (strpos($uri, '/public') === 0) {
            $uri = substr($uri, 7);
        }

        if ($uri === '') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];

            if (is_array($callback)) {
                $controller = new $callback[0]();
                $method = $callback[1];
                return call_user_func([$controller, $method]);
            }
        }

        // Error 404 jika route tidak ditemukan
        http_response_code(404);
        $errorFile = __DIR__ . '/../../views/error/404.php';
        if (file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            echo "404 Not Found";
        }
    }
}