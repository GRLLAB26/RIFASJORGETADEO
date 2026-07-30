<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/helpers.php';

use App\Core\Router;

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);