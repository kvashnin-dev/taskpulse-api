<?php

declare(strict_types=1);

namespace app\modules\task\services;

use app\models\Task;
use app\models\User;
use app\modules\task\forms\TaskForm;
use app\modules\task\forms\TaskSearchForm;
use app\modules\task\repositories\TaskRepositoryInterface;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Connection;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Сервис управления задачами.
 */
final class TaskService
{
    /**
     * @param TaskRepositoryInterface $repository
     * @param Connection $db
     */
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
        private readonly Connection $db,
    ) {}

    /**
     * Создать задачу.
     *
     * @param TaskForm $form
     * @return Task
     * @throws ServerErrorHttpException
     */
    public function create(TaskForm $form): Task
    {
        $task = new Task();
        $task->setAttribute('author_id', (int) $form->authorId);
        $task->title = (string) $form->title;
        $task->description = $form->description === null ? null : (string) $form->description;
        $task->completed = (bool) $form->completed;
        $task->setAttribute('completed_at', $task->completed ? new Expression('CURRENT_TIMESTAMP') : null);

        $this->save($task, Yii::t('task', 'Failed to create task.'));
        $task->refresh();

        return $task;
    }

    /**
     * Получить задачу.
     *
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    public function get(int $id): Task
    {
        return $this->getTask($id);
    }

    /**
     * Получить список задач.
     *
     * @param TaskSearchForm $form
     * @return ActiveDataProvider
     */
    public function getList(TaskSearchForm $form): ActiveDataProvider
    {
        return $this->repository->getList($form);
    }

    /**
     * Получить задачи пользователя.
     *
     * @param int $userId
     * @param TaskSearchForm $form
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function getUserTasks(int $userId, TaskSearchForm $form): ActiveDataProvider
    {
        if (!User::find()->where(['id' => $userId, 'deleted_at' => null])->exists()) {
            throw new NotFoundHttpException(Yii::t('user', 'User not found.'));
        }

        return $this->repository->getList($form, $userId);
    }

    /**
     * Обновить задачу.
     *
     * @param int $id
     * @param TaskForm $form
     * @return Task
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws Throwable
     */
    public function update(int $id, TaskForm $form): Task
    {
        if (!$form->hasField('completed')) {
            $task = $this->getTask($id);
            $this->fill($task, $form);
            $this->save($task, Yii::t('task', 'Failed to update task.'));
            $task->refresh();

            return $task;
        }

        return $this->updateState($id, $form);
    }

    /**
     * Удалить задачу.
     *
     * @param int $id
     * @return void
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function delete(int $id): void
    {
        $task = $this->getTask($id);
        $task->setAttribute('deleted_at', new Expression('CURRENT_TIMESTAMP'));

        $this->save($task, Yii::t('task', 'Failed to delete task.'));
    }

    /**
     * Обновить состояние задачи в транзакции.
     *
     * @param int $id
     * @param TaskForm $form
     * @return Task
     * @throws Throwable
     */
    private function updateState(int $id, TaskForm $form): Task
    {
        $transaction = $this->db->beginTransaction();

        try {
            $task = $this->repository->findForUpdate($id);
            if (!$task instanceof Task) {
                throw new NotFoundHttpException(Yii::t('task', 'Task not found.'));
            }

            $completed = (bool) $form->completed;
            $stateChanged = $task->completed !== $completed;

            $this->fill($task, $form);
            if ($stateChanged) {
                $task->setAttribute(
                    'completed_at',
                    $completed ? new Expression('CURRENT_TIMESTAMP') : null,
                );
            }

            $this->save($task, Yii::t('task', 'Failed to update task.'));
            $transaction->commit();
            $task->refresh();

            return $task;
        } catch (Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Заполнить изменённые поля задачи.
     *
     * @param Task $task
     * @param TaskForm $form
     * @return void
     */
    private function fill(Task $task, TaskForm $form): void
    {
        if ($form->hasField('authorId')) {
            $task->setAttribute('author_id', (int) $form->authorId);
        }
        if ($form->hasField('title')) {
            $task->title = (string) $form->title;
        }
        if ($form->hasField('description')) {
            $task->description = $form->description === null ? null : (string) $form->description;
        }
        if ($form->hasField('completed')) {
            $task->completed = (bool) $form->completed;
        }
    }

    /**
     * Получить задачу или выбросить исключение.
     *
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    private function getTask(int $id): Task
    {
        $task = $this->repository->find($id);
        if (!$task instanceof Task) {
            throw new NotFoundHttpException(Yii::t('task', 'Task not found.'));
        }

        return $task;
    }

    /**
     * Сохранить задачу.
     *
     * @param Task $task
     * @param string $message
     * @return void
     * @throws ServerErrorHttpException
     */
    private function save(Task $task, string $message): void
    {
        if (!$task->save(false)) {
            throw new ServerErrorHttpException($message);
        }
    }
}
