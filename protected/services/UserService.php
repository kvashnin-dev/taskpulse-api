<?php

declare(strict_types=1);

namespace app\services;

use app\dto\users\CreateUserDto;
use app\dto\users\UpdateUserDto;
use app\models\User;
use yii\db\Expression;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

final class UserService
{
    public function create(CreateUserDto $dto): User
    {
        $this->validate($dto);

        $user = new User();
        $user->full_name = trim((string) $dto->full_name);
        $user->phone = $dto->phone === null ? null : (string) $dto->phone;

        if (!$user->save(false)) {
            throw new ServerErrorHttpException('Не удалось создать пользователя.');
        }
        $user->refresh();

        return $user;
    }

    public function get(int $id): User
    {
        $user = User::find()
            ->where(['id' => $id, 'deleted_at' => null])
            ->one();

        if (!$user instanceof User) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * @return array{items: list<array<string, mixed>>, pagination: array{page: int, per_page: int, total: int, total_pages: int}}
     */
    public function getList(int $page, int $perPage): array
    {
        if ($page < 1 || $perPage < 1 || $perPage > 100) {
            throw new BadRequestHttpException('Некорректные параметры пагинации.');
        }

        $query = User::find()->where(['deleted_at' => null]);
        $total = (int) $query->count();
        $users = $query
            ->orderBy(['id' => SORT_ASC])
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->all();

        $items = array_map(
            static fn(User $user): array => $user->toArray(),
            $users,
        );

        return [
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function update(int $id, UpdateUserDto $dto): User
    {
        if (!$dto->hasChanges()) {
            throw new BadRequestHttpException('Не переданы данные для обновления.');
        }

        $this->validate($dto);
        $user = $this->get($id);

        if ($dto->hasField('full_name')) {
            $user->full_name = trim((string) $dto->full_name);
        }
        if ($dto->hasField('phone')) {
            $user->phone = $dto->phone === null ? null : (string) $dto->phone;
        }

        if (!$user->save(false)) {
            throw new ServerErrorHttpException('Не удалось обновить пользователя.');
        }
        $user->refresh();

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->get($id);
        $user->setAttribute('deleted_at', new Expression('CURRENT_TIMESTAMP'));

        if (!$user->save(false)) {
            throw new ServerErrorHttpException('Не удалось удалить пользователя.');
        }
    }

    private function validate(CreateUserDto|UpdateUserDto $dto): void
    {
        if ($dto->validate()) {
            return;
        }

        $errors = $dto->getFirstErrors();
        $message = reset($errors);

        throw new UnprocessableEntityHttpException(
            is_string($message) ? $message : 'Данные не прошли проверку.',
        );
    }
}
