<?php declare(strict_types=1);

require_once './vendor/autoload.php';

use PerfectApp\Container\Container;
use PerfectApp\Logger\FileLogger;
use PerfectApp\Routing\Router;

$logger = new FileLogger('errors.log');

$container = new Container();

$router = new Router($container);
$router->autoRegisterControllers(__DIR__ . '/src/Controllers');

// A user-defined exception handler function
$router->setNotFoundHandler(function ($requestUri, $requestMethod) use ($logger) {
    $logger->error("Route $requestUri with method $requestMethod not found.");
    http_response_code(404);
    echo "Route $requestUri with method $requestMethod not found.";
});

try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (RuntimeException $e) {
    $logger->error($e->getMessage());
    http_response_code(404);
    echo "Route not found.";
}
