<?php

declare(strict_types=1);

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\modules\user\forms\UserForm;
use app\modules\user\forms\UserSearchForm;
use app\modules\user\Module;
use app\modules\user\services\UserService;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Управление пользователями.
 */
final class UserController extends BaseController
{
    private readonly UserService $userService;

    /**
     * @param string $id
     * @param Module $module
     * @param array<string, mixed> $config
     * @throws InvalidConfigException
     */
    public function __construct(string $id, Module $module, array $config = [])
    {
        /** @var UserService $userService */
        $userService = $module->get(UserService::class);
        $this->userService = $userService;

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
            'view' => ['GET'],
            'create' => ['POST'],
            'update' => ['PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Получить список пользователей.
     *
     * @return ActiveDataProvider|UserSearchForm
     */
    public function actionIndex(): ActiveDataProvider|UserSearchForm
    {
        $form = new UserSearchForm();
        $form->load($this->request->getQueryParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        return $this->userService->getList();
    }

    /**
     * Получить пользователя.
     *
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): User
    {
        return $this->userService->get($id);
    }

    /**
     * Создать пользователя.
     *
     * @return User|UserForm
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): User|UserForm
    {
        $form = new UserForm(['scenario' => UserForm::SCENARIO_CREATE]);
        $form->load($this->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        $user = $this->userService->create($form);
        $this->response->setStatusCode(self::CREATED);

        return $user;
    }

    /**
     * Обновить пользователя.
     *
     * @param int $id
     * @return User|UserForm
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(int $id): User|UserForm
    {
        $form = new UserForm(['scenario' => UserForm::SCENARIO_UPDATE]);
        $form->load($this->request->getBodyParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        return $this->userService->update($id, $form);
    }

    /**
     * Удалить пользователя.
     *
     * @param int $id
     * @return void
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id): void
    {
        $this->userService->delete($id);
        $this->response->setStatusCode(self::NO_CONTENT);
    }
}
