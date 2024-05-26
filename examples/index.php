<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use PerfectApp\Container\Container;
use PerfectApp\Controllers\HomeController;
use PerfectApp\Controllers\UserController;
use PerfectApp\Logger\FileLogger;
use PerfectApp\Routing\Router;
use PerfectApp\Services\UserService;

$logger = new FileLogger('errors.log');

$container = new Container($logger);

// Bindings
$container->bind(UserService::class, UserService::class);
$container->bind(UserController::class, UserController::class);
$container->bind(HomeController::class, HomeController::class);

$router = new Router($container);
$router->autoRegisterControllers(__DIR__ . '/src/Controllers');

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];


// A user-defined exception handler function
$router->setNotFoundHandler(function ($requestUri, $requestMethod) use ($logger) {
    $logger->error("Route $requestUri with method $requestMethod not found.");
    http_response_code(404);
    echo "Route $requestUri with method $requestMethod not found.";
});

try {
$router->dispatch($requestUri, $requestMethod);
} catch (RuntimeException $e) {
    // Log the error
    $logger->error($e->getMessage());
    // Send a generic 404 response if no user-defined handler is set
    http_response_code(404);
    echo "Route not found.";
}
