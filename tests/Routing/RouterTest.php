<?php declare(strict_types=1);

namespace Tests\Routing;

use ReflectionException;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use SplFileInfo;
use Exception;
use PerfectApp\Container\Container;
use PerfectApp\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\DummyController;
use TypeError;

#[CoversClass(Router::class)]
#[CoversClass(Container::class)]
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private Router $router;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        // Using PHPUnit's built-in createMock method to create a mock of Container
        $this->container = $this->createMock(Container::class);
        $this->router = new Router($this->container);
    }

    /**
     * Helper method to invoke private/protected methods for testing.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call.
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws ReflectionException
     */
    public function invokeMethod(object $object, string $methodName, array $parameters = array()): mixed
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testAutoRegisterControllersWithInvalidDirectory(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The directory non-existent-directory does not exist');

        $this->router->autoRegisterControllers('non-existent-directory');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testAutoRegisterControllers_DirectoryDoesNotExist_ThrowsException(): void
    {
        // Define a non-existent directory
        $nonExistentDirectory = '/path/to/non-existent-directory';

        // Expect a RuntimeException when calling autoRegisterControllers with a non-existent directory
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The directory $nonExistentDirectory does not exist");

        // Call autoRegisterControllers with the non-existent directory
        $this->router->autoRegisterControllers($nonExistentDirectory);
    }

    /**
     * @coversNothing
     * @throws Exception
     */
    public function testAutoRegisterControllers_ExistingDirectory(): void
    {
        // Define an existing directory (ensure this directory exists for the test)
        $existingDirectory = './tests/Fixtures';

        // Call autoRegisterControllers with the existing directory
        $this->router->autoRegisterControllers($existingDirectory);

        // Assert that no RuntimeException is thrown
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsValidFile_ValidPhpFile(): void
    {
        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getExtension')->willReturn('php');
        $mockFile->method('isDir')->willReturn(false);
        $mockFile->method('getFilename')->willReturn('ValidController.php');

        $isValid = $this->invokeMethod($this->router, 'isValidFile', [$mockFile]);

        $this->assertTrue($isValid);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsValidFile_NonPhpFile(): void
    {
        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getExtension')->willReturn('txt'); // Non-PHP extension
        $mockFile->method('isDir')->willReturn(false);
        $mockFile->method('getFilename')->willReturn('NonPhpFile.txt');

        $isValid = $this->invokeMethod($this->router, 'isValidFile', [$mockFile]);

        $this->assertFalse($isValid);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsValidFile_Directory(): void
    {
        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getExtension')->willReturn('');
        $mockFile->method('isDir')->willReturn(true); // Directory instead of a file
        $mockFile->method('getFilename')->willReturn('Directory');

        $isValid = $this->invokeMethod($this->router, 'isValidFile', [$mockFile]);

        $this->assertFalse($isValid);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsValidFile_HiddenFile(): void
    {
        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getExtension')->willReturn('php');
        $mockFile->method('isDir')->willReturn(false);
        $mockFile->method('getFilename')->willReturn('.HiddenFile.php');

        $isValid = $this->invokeMethod($this->router, 'isValidFile', [$mockFile]);

        $this->assertFalse($isValid);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsValidFile_EmptyFile(): void
    {
        // Mocking an empty SplFileInfo object
        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getExtension')->willReturn('');
        $mockFile->method('isDir')->willReturn(false);
        $mockFile->method('getFilename')->willReturn('');

        $isValid = $this->invokeMethod($this->router, 'isValidFile', [$mockFile]);

        $this->assertFalse($isValid);
    }

    /**
     * @throws ReflectionException
     */
    public function testIsValidFile_NullFile(): void
    {
        $this->expectException(TypeError::class);

        // Use reflection to access and invoke the private isValidFile method
        $method = new ReflectionMethod(Router::class, 'isValidFile');
        $method->invoke($this->router, null);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRegisterControllerFromFile_InvalidFile(): void
    {
        $nonExistentFile = '/path/to/non-existent-file.php';

        $mockFile = $this->createMock(SplFileInfo::class);
        $mockFile->method('getPathname')->willReturn($nonExistentFile);
        $mockFile->method('isFile')->willReturn(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File $nonExistentFile does not exist.");

        $this->invokeMethod($this->router, 'registerControllerFromFile', [$mockFile]);
    }

    /**
     * @return void
     */
    public function testGetNamespaceFromFile_NoNamespace(): void
    {
        $filePath ='./tests/Fixtures/NonNamespacedClass.php';

        // Ensure the file path is valid and readable
        $this->assertFileExists($filePath, "File not found: $filePath");

        // Invoke the private method getNamespaceFromFile
        $reflectionRouter = new ReflectionClass($this->router);
        $method = $reflectionRouter->getMethod('getNamespaceFromFile');

        try {
            $namespace = $method->invoke($this->router, $filePath);
            $this->assertNull($namespace, "Expected null namespace but got: $namespace");
        } catch (ReflectionException $e) {
            $this->fail("Failed to invoke getNamespaceFromFile: " . $e->getMessage());
        }
    }

    /**
     * @throws ReflectionException
     */
    public function testGetNamespaceFromFile_WithNamespace(): void
    {
        $filePath ='./tests/Fixtures/WithNamespaceController.php';
        $namespace = $this->invokeMethod($this->router, 'getNamespaceFromFile', [$filePath]);

        $this->assertEquals('Tests\Fixtures', $namespace);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRegisterController_InvalidController_ThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to create Controller ReflectionClass for InvalidController: Class "InvalidController" does not exist');

        $this->router->registerController('InvalidController');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testDispatch_WithMatchingRoute(): void
    {
        // Create a mock for the dummy controller
        $dummyController = $this->createMock(DummyController::class);
        $dummyController->expects($this->once())
            ->method('someMethod')
            ->with('param1', 'param2'); // Adjust parameters as per your route requirements

        // Expect the container to return the dummy controller
        $this->container->expects($this->once())
            ->method('get')
            ->with(DummyController::class)
            ->willReturn($dummyController);

        // Use reflection to set the routes property directly
        $routes = [
            [
                'path' => '/some/path/(\w+)/(\w+)',
                'methods' => ['GET'],
                'controller' => DummyController::class,
                'action' => 'someMethod',
            ]
        ];

        $reflectionRouter = new ReflectionClass($this->router);
        $property = $reflectionRouter->getProperty('routes');
        $property->setValue($this->router, $routes);

        // Dispatch the route with parameters in the path
        $this->router->dispatch('/some/path/param1/param2', 'GET');
    }

    /**
     * @return void
     */
    public function testDispatch_WithNotFoundHandler(): void
    {
        $notFoundHandlerCalled = false;
        $this->router->setNotFoundHandler(function () use (&$notFoundHandlerCalled) {
            $notFoundHandlerCalled = true;
        });

        try {
            $this->router->dispatch('/non-existent-route', 'GET');
        } catch (Exception) {

        }

        $this->assertTrue($notFoundHandlerCalled);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDispatch_NoMatchingRoute_ThrowsException(): void
    {
        $requestUri = '/invalid-route';
        $requestMethod = 'GET';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Route $requestUri with method GET not found.");

        $this->router->dispatch($requestUri, $requestMethod);
    }
}
