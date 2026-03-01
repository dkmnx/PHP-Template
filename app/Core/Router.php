<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    public function get($uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = rtrim($config['base_url'], '/');

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($baseUrl) {
            $uri = str_replace($baseUrl, '', $uri);
        }
        $uri = $uri ?: '/';

        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        [$controller, $method] = explode('@', $action);
        $controller = "App\\Controllers\\$controller";

        call_user_func([new $controller, $method]);
    }
}
