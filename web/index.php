<?php

declare(strict_types=1);

use yii\web\Application;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/protected/config/bootstrap.php';
require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__) . '/protected/config/web.php';

(new Application($config))->run();
