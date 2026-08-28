<?php

declare(strict_types=1);

use yii\caching\FileCache;
use yii\log\FileTarget;

$db = require __DIR__ . '/db.php';
$i18n = require __DIR__ . '/i18n.php';
$params = require __DIR__ . '/params.php';

return [
    'id' => 'taskpulse-console',
    'name' => $params['appName'],
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'controllerNamespace' => 'app\\commands',
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'db' => $db,
        'i18n' => $i18n,
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => '@runtime/logs/console.log',
                    'logVars' => [],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => '@app/migrations',
        ],
    ],
    'params' => $params,
];
