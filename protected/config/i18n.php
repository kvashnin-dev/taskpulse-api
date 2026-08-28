<?php

declare(strict_types=1);

use yii\i18n\PhpMessageSource;

return [
    'translations' => [
        'user*' => [
            'class' => PhpMessageSource::class,
            'basePath' => '@app/messages',
            'fileMap' => [
                'user' => 'user.php',
            ],
        ],
    ],
];
