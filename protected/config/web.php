<?php

declare(strict_types=1);

use app\services\HealthCheck\HealthCheckService;
use app\services\UserService;
use yii\base\InvalidConfigException;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\log\FileTarget;
use yii\rest\Serializer;
use yii\rest\UrlRule;
use yii\web\JsonParser;
use yii\web\Response;

$db = require __DIR__ . '/db.php';
$params = require __DIR__ . '/params.php';

return [
    'id' => 'taskpulse-api',
    'name' => $params['appName'],
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'controllerNamespace' => 'app\\controllers',
    'bootstrap' => ['log'],
    'container' => [
        'definitions' => [
            Serializer::class => [
                'class' => Serializer::class,
                'collectionEnvelope' => 'items',
            ],
        ],
        'singletons' => [
            UserService::class => UserService::class,
        ],
    ],
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'db' => $db,
        'healthCheckService' => static function (): HealthCheckService {
            $db = Yii::$app->get('db', false);
            if (!$db instanceof Connection) {
                throw new InvalidConfigException('Компонент базы данных не настроен.');
            }

            return new HealthCheckService($db);
        },
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => '@runtime/logs/app.log',
                    'logVars' => [],
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => $_ENV['APP_COOKIE_VALIDATION_KEY'] ?? '',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'health' => 'health/index',
                [
                    'class' => UrlRule::class,
                    'controller' => 'user',
                ],
            ],
        ],
    ],
    'params' => $params,
];
