<?php declare(strict_types=1);

namespace PerfectApp\Exception;

use RuntimeException;

class ControllerReflectionException extends RuntimeException
{
    public function __construct(string $controllerName, string $message)
    {
        parent::__construct("Failed to create Controller ReflectionClass for $controllerName: $message");
    }
}
