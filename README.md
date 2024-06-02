# PerfectRouter

## Description

PerfectRouter is a PHP routing library that provides a simple and efficient way to handle HTTP requests in your web application. It allows developers to define routes using attributes and automatically register controllers and their methods as route handlers. PerfectRouter works seamlessly with PerfectContainer to manage dependencies and create controller instances.

## Features

- **Attribute-Based Routing**: Define routes directly in controller methods using PHP 8 attributes.
- **Auto-Registration**: Automatically register controllers and their routes from a specified directory.
- **Dynamic Route Parameters**: Capture dynamic parameters directly from the URL.
- **HTTP Method Handling**: Define routes for specific HTTP methods (GET, POST, etc.)
- **Custom 404 Handling**: Easily customize 404 Not Found responses.

## Installation

Use Composer to install the PerfectRouter library.

```bash
composer require krubio/perfect-router
```

## Examples

You can find examples in the `examples` directory of the project. Run `composer install` from the `examples` directory to install the required dependencies.

```bash
cd examples
composer install
````

## Usage

Here's a basic usage example of PerfectRouter:

```php
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
```

## Contributing

Contributions, issues, and feature requests are welcome!

## License

This project is [MIT](LICENSE) licensed.