<?php

declare(strict_types=1);

namespace app\modules\task\repositories;

use app\models\Task;
use app\modules\task\forms\TaskSearchForm;
use yii\data\ActiveDataProvider;

/**
 * Репозиторий задач.
 */
interface TaskRepositoryInterface
{
    /**
     * Найти задачу.
     *
     * @param int $id
     * @return Task|null
     */
    public function find(int $id): ?Task;

    /**
     * Найти и заблокировать задачу до завершения транзакции.
     *
     * @param int $id
     * @return Task|null
     */
    public function findForUpdate(int $id): ?Task;

    /**
     * Получить список задач.
     *
     * @param TaskSearchForm $form
     * @param int|null $authorId
     * @return ActiveDataProvider
     */
    public function getList(TaskSearchForm $form, ?int $authorId = null): ActiveDataProvider;
}
