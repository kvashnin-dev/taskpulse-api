<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\users\CreateUserDto;
use app\dto\users\UpdateUserDto;
use app\services\UserService;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

final class UserController extends BaseController
{
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

    /**
     * @return array{items: list<array<string, mixed>>, pagination: array{page: int, per_page: int, total: int, total_pages: int}}
     * @throws BadRequestHttpException|InvalidConfigException|ServerErrorHttpException
     */
    public function actionIndex(): array
    {
        $page = $this->getPositiveQueryParam('page', 1);
        $perPage = $this->getPositiveQueryParam('per_page', 20);
        $userService = $this->getUserService();

        return $userService->getList($page, $perPage);
    }

    /**
     * @return array<string, mixed>
     * @throws InvalidConfigException|ServerErrorHttpException
     */
    public function actionView(int $id): array
    {
        $userService = $this->getUserService();
        $user = $userService->get($id);

        return $user->toArray();
    }

    /**
     * @return array<string, mixed>
     * @throws InvalidConfigException|ServerErrorHttpException
     */
    public function actionCreate(): array
    {
        $dto = CreateUserDto::fromArray($this->getBodyParams());
        $userService = $this->getUserService();
        $user = $userService->create($dto);
        $this->response->setStatusCode(self::CREATED);

        return $user->toArray();
    }

    /**
     * @return array<string, mixed>
     * @throws InvalidConfigException|ServerErrorHttpException
     */
    public function actionUpdate(int $id): array
    {
        $dto = UpdateUserDto::fromArray($this->getBodyParams());
        $userService = $this->getUserService();
        $user = $userService->update($id, $dto);

        return $user->toArray();
    }

    /**
     * @throws InvalidConfigException|ServerErrorHttpException
     */
    public function actionDelete(int $id): null
    {
        $userService = $this->getUserService();
        $userService->delete($id);
        $this->response->setStatusCode(self::NO_CONTENT);

        return null;
    }

    /**
     * @return array<string, mixed>
     * @throws BadRequestHttpException
     */
    private function getBodyParams(): array
    {
        $bodyParams = $this->request->getBodyParams();
        if (!is_array($bodyParams)) {
            throw new BadRequestHttpException('Тело запроса должно быть JSON-объектом.');
        }

        return $bodyParams;
    }

    /**
     * @throws BadRequestHttpException
     */
    private function getPositiveQueryParam(string $name, int $default): int
    {
        $value = $this->request->getQueryParam($name, $default);
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1) {
            throw new BadRequestHttpException("Параметр {$name} должен быть положительным целым числом.");
        }

        return (int) $value;
    }

    /**
     * @throws InvalidConfigException|ServerErrorHttpException
     */
    private function getUserService(): UserService
    {
        $userService = Yii::$app->get('userService', false);
        if (!$userService instanceof UserService) {
            throw new ServerErrorHttpException('Сервис пользователей не настроен.');
        }

        return $userService;
    }
}
