<?php declare(strict_types=1);

namespace PerfectApp\Routing;

use Exception;
use PerfectApp\Container\Container;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;


class Router
{
    private const FATAL_ERROR_MESSAGE = 'Fatal Error. See Error log for details.';

    private array $routes = [];
    private Container $container;
    private LoggerInterface $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function autoRegisterControllers(string $directory): void
    {
        if (!is_dir($directory)) {
            $this->logger->error("The directory $directory does not exist");
            throw new RuntimeException(self::FATAL_ERROR_MESSAGE);
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($file->isDir() || $file->getFilename()[0] === '.' || $file->getExtension() !== 'php') {
                continue;
            }

            require_once $file->getPathname();
            $className = basename($file->getPathname(), '.php');
            $namespace = $this->getNamespaceFromFile($file->getPathname());
            $fullyQualifiedClassName = $namespace ? $namespace . '\\' . $className : $className;

            if (class_exists($fullyQualifiedClassName)) {
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
            $this->logger->error("Failed to create Controller ReflectionClass for $controllerName: {$e->getMessage()}");
            http_response_code(500);
            die(self::FATAL_ERROR_MESSAGE);
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

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        foreach ($this->routes as $routeInfo) {
            $pattern = "@^" . str_replace("/", "\\/", $routeInfo['path']) . "$@";

            if (in_array($requestMethod, $routeInfo['methods']) && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);

                $controllerName = $routeInfo['controller'];
                $methodName = $routeInfo['action'];

                try {
                    $controller = $this->container->get($controllerName);
                    call_user_func_array([$controller, $methodName], $matches);
                } catch (Exception $e) {
                    $this->logger->error("Failed to dispatch controller: {$e->getMessage()}");
                    http_response_code(500);
                    die(self::FATAL_ERROR_MESSAGE);
                }

                return;
            }
        }

        echo "PerfectApp\Routing\Route $requestUri with method $requestMethod not found.\n";
    }
}
