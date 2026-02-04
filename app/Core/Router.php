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
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            // Get and sanitize the URI
            $rawUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = $this->sanitizeUri($rawUri);

            $action = $this->routes[$method][$uri] ?? null;

            if (!$action) {
                http_response_code(404);
                echo "404 Not Found";
                return;
            }

            // Handle closure callbacks
            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            // Handle controller@method string format
            [$controller, $method] = explode('@', $action);
            $controller = "App\\Controllers\\$controller";

            call_user_func([new $controller, $method]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo 'Bad Request';
        }
    }

    /**
     * Sanitize URI to prevent path traversal and injection attacks
     *
     * @param string $uri Raw URI from REQUEST_URI
     * @return string Sanitized URI path
     * @throws \InvalidArgumentException If URI contains malicious patterns
     */
    private function sanitizeUri($uri)
    {
        // Remove base path (make configurable later, hardcoded for now)
        $basePath = '/iQMS/public';
        $uri = str_replace($basePath, '', $uri);

        // Ensure URI starts with / or is empty
        $uri = $uri ?: '/';

        // Remove null bytes
        $uri = str_replace("\0", '', $uri);

        // Decode URL-encoded characters for validation
        $decoded = rawurldecode($uri);

        // Block path traversal attempts
        if (preg_match('#\.\./#', $decoded) || preg_match('#\.\.\\\#', $decoded)) {
            http_response_code(400);
            throw new \InvalidArgumentException('Path traversal detected');
        }

        // Only allow safe characters: alphanumeric, hyphen, underscore, slash, dot
        if (!preg_match('#^[\w\-/.]+$#', $uri)) {
            http_response_code(400);
            throw new \InvalidArgumentException('Invalid URI characters');
        }

        return $uri;
    }
}
