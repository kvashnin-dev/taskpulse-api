<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\users\CreateUserDto;
use app\dto\users\UpdateUserDto;
use app\forms\UserSearchForm;
use app\models\User;
use app\services\UserService;
use yii\base\Module;
use yii\data\ActiveDataProvider;

final class UserController extends BaseController
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        string $id,
        Module $module,
        private readonly UserService $userService,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
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

    public function actionIndex(): ActiveDataProvider|UserSearchForm
    {
        $form = new UserSearchForm();
        $form->load($this->request->getQueryParams(), '');

        if (!$form->validate()) {
            return $form;
        }

        return $this->userService->getList($form);
    }

    public function actionView(int $id): User
    {
        return $this->userService->get($id);
    }

    public function actionCreate(): User|CreateUserDto
    {
        $dto = new CreateUserDto();
        $dto->load($this->request->getBodyParams(), '');

        if (!$dto->validate()) {
            return $dto;
        }

        $user = $this->userService->create($dto);
        $this->response->setStatusCode(self::CREATED);

        return $user;
    }

    public function actionUpdate(int $id): User|UpdateUserDto
    {
        $dto = new UpdateUserDto();
        $dto->load($this->request->getBodyParams(), '');

        if (!$dto->validate()) {
            return $dto;
        }

        return $this->userService->update($id, $dto);
    }

    public function actionDelete(int $id): void
    {
        $this->userService->delete($id);
        $this->response->setStatusCode(self::NO_CONTENT);
    }
}
