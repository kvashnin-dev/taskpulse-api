<?php

declare(strict_types=1);

namespace app\services;

use app\dto\users\CreateUserDto;
use app\dto\users\UpdateUserDto;
use app\forms\UserSearchForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

final class UserService
{
    public function create(CreateUserDto $dto): User
    {
        $user = new User();
        $user->full_name = (string) $dto->full_name;
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

    public function getList(UserSearchForm $form): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => User::find()
                ->where(['deleted_at' => null])
                ->orderBy(['id' => SORT_ASC]),
            'pagination' => [
                'page' => (int) $form->page - 1,
                'pageSize' => (int) $form->per_page,
                'pageSizeLimit' => [1, 100],
                'pageParam' => 'page',
                'pageSizeParam' => 'per_page',
            ],
        ]);
    }

    public function update(int $id, UpdateUserDto $dto): User
    {
        $user = $this->get($id);

        if ($dto->hasField('full_name')) {
            $user->full_name = (string) $dto->full_name;
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
}
