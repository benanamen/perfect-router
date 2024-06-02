<?php declare(strict_types=1);

namespace PerfectApp\Exception;

use RuntimeException;

class RouteNotFoundException extends RuntimeException
{
    public function __construct(string $requestUri, string $requestMethod)
    {
        parent::__construct("Route $requestUri with method $requestMethod not found.");
    }
}
