[![codecov](https://codecov.io/gh/benanamen/perfect-logger/branch/master/graph/badge.svg?token=JRkrD9z3fi)](https://codecov.io/gh/benanamen/perfect-logger)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/benanamen/perfect-logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/benanamen/perfect-logger/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/benanamen/perfect-logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/benanamen/perfect-logger/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/benanamen/perfect-logger/badges/build.png?b=master)](https://scrutinizer-ci.com/g/benanamen/perfect-logger/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/benanamen/perfect-logger/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=coverage)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)

[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=bugs)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=benanamen_perfect-logger&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=benanamen_perfect-logger)


# Perfect Logger

Perfect Logger is a PSR-3 compliant logging library for PHP. It provides an easy way to log messages to a file.

## Installation

Install via composer:

```shell
composer require krubio/perfect-logger
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use PerfectApp\Logger\FileLogger;

// Initialize the logger
$logger = new FileLogger('/path/to/your/logfile.log');

// Log some messages
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
```

## Log Levels

The logger supports the following log levels:

- Emergency: system is unusable
- Alert: action must be taken immediately
- Critical: critical conditions
- Error: error conditions
- Warning: warning conditions
- Notice: normal but significant condition
- Info: informational messages
- Debug: debug-level messages

## License

The MIT License (MIT). Please see License File for more information.
