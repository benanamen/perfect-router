<?php declare(strict_types=1);

namespace PerfectApp\Routing;

use Exception;
use PerfectApp\Container\Container;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use SplFileInfo;

/* This seems to be version 2 from autowire version.
Changes are throwing exceptions instead of die and sending error to error log.
Changes made for Unit testing compatability

*/

class Router
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Auto-registers controllers from a specified directory.
     *
     * @param string $directory The directory to scan for controller files.
     * @throws Exception If the directory does not exist.
     */
    public function autoRegisterControllers(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new RuntimeException("The directory $directory does not exist");
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($this->isValidFile($file)) {
                $this->registerControllerFromFile($file);
            }
        }
    }

    /**
     * Validates if a file should be considered for controller registration.
     *
     * @param SplFileInfo $file The file to validate.
     * @return bool True if the file is valid, false otherwise.
     */
    private function isValidFile(SplFileInfo $file): bool
    {
        return !$file->isDir() && $file->getFilename()[0] !== '.' && $file->getExtension() === 'php';
    }

    /**
     * Registers a controller from a file.
     *
     * @param SplFileInfo $file The file to register the controller from.
     * @throws Exception
     */
    private function registerControllerFromFile(SplFileInfo $file): void
    {
        require_once $file->getPathname();
        $className = basename($file->getPathname(), '.php');
        $namespace = $this->getNamespaceFromFile($file->getPathname());
        $fullyQualifiedClassName = $namespace ? $namespace . '\\' . $className : $className;

        if (class_exists($fullyQualifiedClassName)) {
            $this->registerController($fullyQualifiedClassName);
        }
    }

    /**
     * Extracts the namespace from a PHP file.
     *
     * @param string $filePath The path to the PHP file.
     * @return string|null The namespace if found, null otherwise.
     */
    private function getNamespaceFromFile(string $filePath): ?string
    {
        $src = file_get_contents($filePath);
        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $insideNamespaceDeclaration = false;

        while ($i < $count) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $insideNamespaceDeclaration = true;
                $i++;
                continue;
            }

            if ($insideNamespaceDeclaration) {
                if ($tokens[$i][0] === T_WHITESPACE) {
                    $i++;
                    continue;
                }

                if ($tokens[$i][0] === ';') {
                    return $namespace;
                }

                if (is_array($tokens[$i])) {
                    $namespace .= $tokens[$i][1];
                }
            }

            $i++;
        }

        return null;
    }

    /**
     * Registers a controller and its routes.
     *
     * @param string $controllerName The fully qualified name of the controller.
     * @throws Exception If the reflection class cannot be created.
     */
    public function registerController(string $controllerName): void
    {
        try {
            $reflectionClass = new ReflectionClass($controllerName);
        } catch (ReflectionException $e) {
            throw new RuntimeException("Failed to create Controller ReflectionClass for $controllerName: {$e->getMessage()}");
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $routeAttributes = $method->getAttributes(Route::class);
            foreach ($routeAttributes as $routeAttribute) {
                $routeData = $routeAttribute->newInstance();
                $this->routes[] = [
                    'path' => $routeData->path,
                    'methods' => $routeData->methods,
                    'controller' => $controllerName,
                    'action' => $method->getName(),
                ];
            }
        }
    }


    /**
     * Dispatches the request to the appropriate controller action.
     *
     * @param string $requestUri The requested URI.
     * @param string $requestMethod The HTTP method of the request.
     * @throws Exception If no route matches the request.
     */
    public function dispatch(string $requestUri, string $requestMethod): void
    {
        foreach ($this->routes as $routeInfo) {
            $pattern = "@^" . str_replace("/", "\\/", $routeInfo['path']) . "$@";

            if (in_array($requestMethod, $routeInfo['methods']) && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove the entire string that was matched

                $controllerName = $routeInfo['controller'];
                $methodName = $routeInfo['action'];

                $controller = $this->container->get($controllerName);
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        throw new RuntimeException("Route $requestUri with method $requestMethod not found.");
    }
}
