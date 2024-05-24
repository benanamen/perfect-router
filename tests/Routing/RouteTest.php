<?php

declare(strict_types=1);

namespace Tests\Routing;

use PerfectApp\Routing\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Route::class)]
class RouteTest extends TestCase
{
    // Test method for Route attribute class
    public function testRouteAttribute(): void
    {
        // Test case for Route attribute
        $route = new Route('/test-route', ['GET', 'POST']);

        // Assertions can go here to test the behavior of the Route attribute
        $this->assertEquals('/test-route', $route->path);
        $this->assertEquals(['GET', 'POST'], $route->methods);
    }
}
