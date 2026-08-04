<?php

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../../routes/web.php';
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            exit('404 - Página no encontrada');
        }

        [$controller, $action] = $this->routes[$method][$uri];

        $controller = new $controller();

        $controller->$action();
    }
}
