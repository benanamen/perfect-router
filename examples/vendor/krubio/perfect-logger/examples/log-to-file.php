<?php declare(strict_types=1);

use PerfectApp\Logger\FileLogger;

require '../vendor/autoload.php';

$logger = new FileLogger('logfile.log');

$logger->emergency('This is an emergency message');
$logger->alert('This is an alert message');
$logger->critical('This is a critical message');
$logger->error('An error occurred', ['errorCode' => 123]);
$logger->warning('This is a warning message');
$logger->notice('This is a notice message');
$logger->info('This is an informational message');
$logger->debug('This is a debug message');

$logger->error('An error occurred', [
    'user_id' => 10,
    'url' => 'https://example.com',
    'data' => 'Important Data'
]);

echo 'View Logfile';
