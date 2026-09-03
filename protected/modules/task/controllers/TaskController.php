<?php

declare(strict_types=1);

namespace app\modules\task\controllers;

use app\controllers\BaseController;
use app\models\Task;
use app\modules\task\forms\TaskForm;
use app\modules\task\forms\TaskSearchForm;
use app\modules\task\Module;
use app\modules\task\services\TaskService;
use Throwable;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Управление задачами.
 */
final class TaskController extends BaseController
{
    private readonly TaskService $taskService;

    /**
     * @param string $id
     * @param Module $module
     * @param array<string, mixed> $config
     * @throws InvalidConfigException
     */
    public function __construct(string $id, Module $module, array $config = [])
    {
        /** @var TaskService $taskService */
        $taskService = $module->get(TaskService::class);
        $this->taskService = $taskService;

        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     * @return array<string, list<string>>
     */
    protected function verbs(): array
    {
        return [
            'index' => ['GET'],
            'user' => ['GET'],
            'view' => ['GET'],
            'create' => ['POST'],
            'update' => ['PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Получить список задач.
     *
     * @return ActiveDataProvider|TaskSearchForm
     */
    public function actionIndex(): ActiveDataProvider|TaskSearchForm
    {
        $form = $this->getSearchForm();
        if (!$form->validate()) {
            return $form;
        }

        return $this->taskService->getList($form);
    }

    /**
     * Получить задачи пользователя.
     *
     * @param int $id
     * @return ActiveDataProvider|TaskSearchForm
     * @throws NotFoundHttpException
     */
    public function actionUser(int $id): ActiveDataProvider|TaskSearchForm
    {
        $form = $this->getSearchForm();
        if (!$form->validate()) {
            return $form;
        }

        return $this->taskService->getUserTasks($id, $form);
    }

    /**
     * Получить задачу.
     *
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Task
    {
        return $this->taskService->get($id);
    }

    /**
     * Создать задачу.
     *
     * @return Task|TaskForm
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): Task|TaskForm
    {
        $form = new TaskForm(['scenario' => TaskForm::SCENARIO_CREATE]);
        $form->load($this->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        $task = $this->taskService->create($form);
        $this->response->setStatusCode(self::CREATED);

        return $task;
    }

    /**
     * Обновить задачу.
     *
     * @param int $id
     * @return Task|TaskForm
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws Throwable
     */
    public function actionUpdate(int $id): Task|TaskForm
    {
        $form = new TaskForm(['scenario' => TaskForm::SCENARIO_UPDATE]);
        $form->load($this->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        return $this->taskService->update($id, $form);
    }

    /**
     * Удалить задачу.
     *
     * @param int $id
     * @return void
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id): void
    {
        $this->taskService->delete($id);
        $this->response->setStatusCode(self::NO_CONTENT);
    }

    /**
     * Создать форму фильтрации.
     *
     * @return TaskSearchForm
     */
    private function getSearchForm(): TaskSearchForm
    {
        $form = new TaskSearchForm();
        $form->load($this->request->getQueryParams(), '');

        return $form;
    }
}
