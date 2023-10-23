<?php declare(strict_types=1);

namespace Routing;

use Exception;
use PerfectApp\Container\Container;
use PerfectApp\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
#[CoversClass(Container::class)]
class RouterTest extends TestCase
{
    private $container;
    private $router;

    public function setUp(): void
    {
        // Using PHPUnit's built-in createMock method to create a mock of Container
        $this->container = $this->createMock(Container::class);
        $this->router = new Router($this->container);
    }
    public function testAutoRegisterControllersWithInvalidDirectory(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The directory non-existent-directory does not exist');

        $this->router->autoRegisterControllers('non-existent-directory');
    }

    public function testRegisterControllerWithInvalidController(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create Controller ReflectionClass for InvalidControllerName: Class "InvalidControllerName" does not exist');

        $this->router->registerController('InvalidControllerName');
    }

    public function testAutoRegisterControllers_DirectoryDoesNotExist_ThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The directory non-existent-directory does not exist');

        $this->router->autoRegisterControllers('non-existent-directory');
    }

    // Additional test methods would go here...

    // Example: Test dispatch with valid and invalid routes, etc.
    public function testDispatch_NoMatchingRoute_ThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route /invalid-route with method GET not found.');

        $this->router->dispatch('/invalid-route', 'GET');
    }

    // Example: Test registerController with valid and invalid controllers, etc.
    public function testRegisterController_InvalidController_ThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create Controller ReflectionClass for InvalidController: Class "InvalidController" does not exist');

        $this->router->registerController('InvalidController');
    }

    // Additional test methods to achieve 100% code coverage would be needed...
}
