<?php

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../../routes/web.php';
    }

    public function dispatch(string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            exit('404 - Página no encontrada');
        }

        [$controller, $action] = $this->routes[$method][$path];

        $instance = new $controller();
        $instance->$action();
    }
}