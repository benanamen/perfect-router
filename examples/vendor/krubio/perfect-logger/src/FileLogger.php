<?php

namespace PerfectApp\Logger;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class FileLogger implements LoggerInterface
{
    /**
     * @param string $filePath
     */
    public function __construct(private readonly string $filePath)
    {
    }

    /**
     * @param mixed $level
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function log(mixed $level, Stringable|string $message, array $context = []): void
    {
        if (!in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG
        ])) {
            throw new InvalidArgumentException('Invalid log level.');
        }

        $logMessage = $this->createLogMessage($level, $message, $context);
        file_put_contents($this->filePath, $logMessage, FILE_APPEND);
    }

    /**
     * @param mixed $level
     * @param Stringable|string $message
     * @param array $context
     * @return string
     */
    private function createLogMessage(mixed $level, Stringable|string $message, array $context): string
    {
        return sprintf(
            '[%s] %s %s %s' . PHP_EOL,
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            json_encode($context)
        );
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @param Stringable|string $message
     * @param array $context
     * @return void
     */
    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
