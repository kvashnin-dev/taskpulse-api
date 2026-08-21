<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$root = dirname(__DIR__, 2);
Dotenv::createImmutable($root)->safeLoad();

$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
$environment = $_ENV['APP_ENV'] ?? 'prod';

defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_ENV') or define('YII_ENV', $environment);
