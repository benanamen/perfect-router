<?php declare(strict_types=1);

namespace Unit\Logger;

use InvalidArgumentException;
use PerfectApp\Logger\FileLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

#[CoversClass(FileLogger::class)]
class FileLoggerTest extends TestCase
{
    private string $logFilePath;
    private FileLogger $logger;

    protected function setUp(): void
    {
        $this->logFilePath = __DIR__ . '/test.log';
        $this->logger = new FileLogger($this->logFilePath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFilePath)) {
            unlink($this->logFilePath);
        }
    }

    public function testLog(): void
    {
        $this->logger->log(LogLevel::INFO, 'Test message', ['key' => 'value']);

        $logContent = file_get_contents($this->logFilePath);

        $this->assertStringContainsString('Test message', $logContent);
        $this->assertStringContainsString('"key":"value"', $logContent);
    }

    public function testInvalidLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->logger->log('invalid', 'Test message');
    }

    /**
     * @dataProvider logLevelProvider
     */
    public function testLogLevelMethods(string $level, string $method): void
    {
        $this->logger->{$method}('Test message', ['key' => 'value']);

        $logContent = file_get_contents($this->logFilePath);

        $this->assertStringContainsString(strtoupper($level), $logContent);
        $this->assertStringContainsString('Test message', $logContent);
        $this->assertStringContainsString('"key":"value"', $logContent);
    }

    public static function logLevelProvider(): array
    {
        return [
            [LogLevel::EMERGENCY, 'emergency'],
            [LogLevel::ALERT, 'alert'],
            [LogLevel::CRITICAL, 'critical'],
            [LogLevel::ERROR, 'error'],
            [LogLevel::WARNING, 'warning'],
            [LogLevel::NOTICE, 'notice'],
            [LogLevel::INFO, 'info'],
            [LogLevel::DEBUG, 'debug'],
        ];
    }
}
