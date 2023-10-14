<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use PerfectApp\Container\Container;
use PerfectApp\Controllers\UserController;
use PerfectApp\Logger\FileLogger;
use PerfectApp\Routing\Router;
use PerfectApp\Services\UserService;

$logger = new FileLogger('errors.log');

$container = new Container($logger);

// Bindings
$container->bind(UserService::class, UserService::class);
$container->bind(UserController::class, UserController::class);

$router = new Router($container);
$router->autoRegisterControllers(__DIR__ . '/src/Controllers');

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestUri, $requestMethod);
