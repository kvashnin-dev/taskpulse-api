<?php

declare(strict_types=1);

namespace app\modules\task;

use app\modules\task\repositories\TaskRepository;
use app\modules\task\repositories\TaskRepositoryInterface;
use app\modules\task\services\TaskService;
use Yii;
use yii\db\Connection;

/**
 * Модуль задач.
 */
final class Module extends \yii\base\Module
{
    /** @inheritDoc */
    public $controllerNamespace = 'app\\modules\\task\\controllers';

    /** @inheritDoc */
    public function init(): void
    {
        parent::init();

        $this->set(TaskRepositoryInterface::class, [
            'class' => TaskRepository::class,
        ]);
        $this->set(TaskService::class, function (): TaskService {
            /** @var TaskRepositoryInterface $repository */
            $repository = $this->get(TaskRepositoryInterface::class);
            /** @var Connection $db */
            $db = Yii::$app->get('db');

            return new TaskService($repository, $db);
        });
    }
}
