<?php declare(strict_types=1);

namespace PerfectApp\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use Psr\Log\LoggerInterface;

class Container
{
    private array $instances = [];
    private array $bindings = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function bind(string $abstract, mixed $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function get(string $className): object
    {
        if (isset($this->bindings[$className])) {
            return $this->resolveBinding($className);
        }

        return $this->resolveClass($className);
    }

    private function resolveBinding(string $className): object
    {
        $concrete = $this->bindings[$className];

        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        return $this->resolveClass($concrete);
    }

    private function resolveClass(string $className): object
    {
        if (!isset($this->instances[$className])) {
            $this->instances[$className] = $this->instantiateClass($className);
        }

        return $this->instances[$className];
    }

    private function instantiateClass(string $className): object
    {
        try {
            $reflectionClass = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            $this->logger->error("Failed to create ReflectionClass for $className: {$e->getMessage()}");
            http_response_code(500);
            die('Fatal Error. See Error log for details.');
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor) {
            $dependencies = $this->resolveDependencies($constructor->getParameters());
            return $reflectionClass->newInstanceArgs($dependencies);
        }

        return new $className();
    }

    private function resolveDependencies(array $params): array
    {
        $dependencies = [];

        foreach ($params as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $dependency = $type->getName();
                $dependencies[] = $this->get($dependency);
            }
        }

        return $dependencies;
    }
}
