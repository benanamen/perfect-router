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

## Usage

Here's a basic usage example of PerfectRouter:

```php
require 'vendor/autoload.php';

use PerfectApp\Routing\Router;
use PerfectApp\Routing\Route;

class MyController {
    #[Route('/my-route', ['GET'])]
    public function myMethod() {
        echo 'Hello, PerfectRouter!';
    }
}

$router = new Router();
$router->registerController(MyController::class);
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```

## Contributing

Contributions, issues, and feature requests are welcome!

## License

This project is [MIT](LICENSE) licensed.