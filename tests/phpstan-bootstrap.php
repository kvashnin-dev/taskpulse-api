<?php

declare(strict_types=1);

defined('YII_DEBUG') or define('YII_DEBUG', (bool) ($_SERVER['YII_DEBUG'] ?? false));
defined('YII_ENV') or define('YII_ENV', (string) ($_SERVER['YII_ENV'] ?? 'test'));

require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';
