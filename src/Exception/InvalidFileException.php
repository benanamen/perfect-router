<?php declare(strict_types=1);

namespace PerfectApp\Exception;

use RuntimeException;

class InvalidFileException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
