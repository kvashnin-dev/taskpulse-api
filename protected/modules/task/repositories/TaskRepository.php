<?php

declare(strict_types=1);

namespace app\modules\task\repositories;

use app\extensions\DbConnectTrait;
use app\models\Task;
use app\modules\task\forms\TaskSearchForm;
use RuntimeException;
use yii\data\SqlDataProvider;

/**
 * Репозиторий задач.
 */
final class TaskRepository
{
    use DbConnectTrait;

    /**
     * Получить задачу.
     *
     * @param int $id
     * @return Task|null
     */
    public function getById(int $id): ?Task
    {
        return Task::find()
            ->where(['id' => $id, 'deleted_at' => null])
            ->one();
    }

    /**
     * Получить и заблокировать задачу до завершения транзакции.
     *
     * @param int $id
     * @return Task|null
     */
    public function getByIdForUpdate(int $id): ?Task
    {
        return Task::findBySql(
            $this->getSql('get_task_for_update'),
            [':id' => $id],
        )->one();
    }

    /**
     * Получить список задач.
     *
     * @param TaskSearchForm $form
     * @param int|null $authorId
     * @return SqlDataProvider
     */
    public function getList(TaskSearchForm $form, ?int $authorId = null): SqlDataProvider
    {
        return new SqlDataProvider([
            'db' => $this->getDbConnection(),
            'sql' => $this->getSql('get_tasks'),
            'params' => [
                ':authorId' => $authorId ?? $form->authorId,
                ':completed' => $form->completed,
                ':createdFrom' => $form->createdFrom,
                ':createdTo' => $form->createdTo,
                ':completedFrom' => $form->completedFrom,
                ':completedTo' => $form->completedTo,
            ],
            'key' => 'id',
            'pagination' => [
                'defaultPageSize' => 20,
                'pageSizeLimit' => [1, 100],
                'pageParam' => 'page',
                'pageSizeParam' => 'perPage',
            ],
            'sort' => [
                'sortParam' => 'sort',
                'defaultOrder' => [
                    'createdAt' => SORT_DESC,
                    'id' => SORT_DESC,
                ],
                'attributes' => [
                    'id',
                    'title',
                    'completed',
                    'createdAt' => [
                        'asc' => ['created_at' => SORT_ASC],
                        'desc' => ['created_at' => SORT_DESC],
                    ],
                    'completedAt' => [
                        'asc' => ['completed_at' => SORT_ASC],
                        'desc' => ['completed_at' => SORT_DESC],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Получить SQL-запрос из файла.
     *
     * @param string $name
     * @return string
     */
    private function getSql(string $name): string
    {
        $sql = file_get_contents(__DIR__ . "/sqls/{$name}.sql");
        if ($sql === false) {
            throw new RuntimeException("Не удалось прочитать SQL-запрос {$name}.");
        }

        return $sql;
    }
}
