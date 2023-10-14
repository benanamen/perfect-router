<?php declare(strict_types=1);

namespace PerfectApp\Routing;

use PerfectApp\Container\Container;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

class Router
{
    private array $routes = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function autoRegisterControllers(string $directory): void
    {
        //var_dump("Attempting to Auto-Register Controllers from: " . $directory);  // Debug
        if (!is_dir($directory)) {
            error_log("The directory $directory does not exist");
            http_response_code(500);
            die('Fatal Error. See Error log for details.');
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {

            if ($file->isDir() || $file->getFilename()[0] === '.' || $file->getExtension() !== 'php') {
                continue;
            }
            //var_dump("Including file: " . $file->getPathname());  // Debug
            require_once $file->getPathname();
            $className = basename($file->getPathname(), '.php');

            //Testing
            $namespace = $this->getNamespaceFromFile($file->getPathname());
            $fullyQualifiedClassName = $namespace ? $namespace . '\\' . $className : $className;

            if (class_exists($fullyQualifiedClassName)) {
                //var_dump("Registering Controller: " . $className);  // Debug
                $this->registerController($fullyQualifiedClassName);
            }
        }
    }

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

    public function registerController(string $controllerName): void
    {
        try {
            $reflectionClass = new ReflectionClass($controllerName);
        } catch (ReflectionException $e) {
            error_log("Failed to create Controller ReflectionClass for $controllerName: {$e->getMessage()}");
            http_response_code(500);
            die('Fatal Error. See Error log for details.');
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

                // Debugging statement to see which routes are being registered
                //var_dump("Reg'd route: " . $routeData->path . " for " . $controllerName . "::" . $method->getName());
            }
        }
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        //var_dump("Dispatching: " . $requestUri . " with Method: " . $requestMethod);  // Debug
        //$matched = false; //debug

        foreach ($this->routes as $routeInfo) {
            $pattern = "@^" . str_replace("/", "\\/", $routeInfo['path']) . "$@";

            if (in_array($requestMethod, $routeInfo['methods']) && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove the entire string that was matched

                //$matched = true;// Debug
                //var_dump("Route Matched: " . $routeInfo['path']);  // Debug

                $controllerName = $routeInfo['controller'];
                $methodName = $routeInfo['action'];

                $controller = $this->container->get($controllerName);
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        //Debug
        /*        if (!$matched) {
                    //var_dump("Route Not Matched");  // Debug
                    header("HTTP/1.0 404 Not Found");
                    //require '404.html';
                    echo "Route $requestUri with method $requestMethod not found.\n";
                }//end debug*/

        echo "PerfectApp\Routing\Route $requestUri with method $requestMethod not found.\n";
    }
}
