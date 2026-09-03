<?php

declare(strict_types=1);

namespace app\modules\task\repositories;

use app\models\Task;
use app\modules\task\forms\TaskSearchForm;
use yii\data\ActiveDataProvider;

/**
 * Репозиторий задач.
 */
final class TaskRepository implements TaskRepositoryInterface
{
    /** @inheritDoc */
    public function find(int $id): ?Task
    {
        return Task::find()
            ->where(['id' => $id, 'deleted_at' => null])
            ->one();
    }

    /** @inheritDoc */
    public function findForUpdate(int $id): ?Task
    {
        return Task::findBySql(
            'SELECT * FROM {{%tasks}} WHERE id = :id AND deleted_at IS NULL FOR UPDATE',
            [':id' => $id],
        )->one();
    }

    /** @inheritDoc */
    public function getList(TaskSearchForm $form, ?int $authorId = null): ActiveDataProvider
    {
        $query = Task::find()
            ->where(['deleted_at' => null])
            ->andFilterWhere(['author_id' => $authorId ?? $form->authorId])
            ->andFilterWhere(['completed' => $form->completed])
            ->andFilterWhere(['>=', 'created_at', $form->createdFrom])
            ->andFilterWhere(['<=', 'created_at', $form->createdTo])
            ->andFilterWhere(['>=', 'completed_at', $form->completedFrom])
            ->andFilterWhere(['<=', 'completed_at', $form->completedTo]);

        return new ActiveDataProvider([
            'query' => $query,
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
}
