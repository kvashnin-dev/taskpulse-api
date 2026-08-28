<?php

declare(strict_types=1);

namespace app\modules\user\services;

use app\models\User;
use app\modules\user\forms\UserForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Сервис управления пользователями.
 */
final class UserService
{
    /**
     * Создать пользователя.
     *
     * @param UserForm $form
     * @return User
     * @throws ServerErrorHttpException
     */
    public function create(UserForm $form): User
    {
        $user = new User();
        $user->setAttribute('full_name', $form->fullName);
        $user->phone = $form->phone === null ? null : (string) $form->phone;

        if (!$user->save(false)) {
            throw new ServerErrorHttpException(Yii::t('user', 'Failed to create user.'));
        }
        $user->refresh();

        return $user;
    }

    /**
     * Получить пользователя.
     *
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function get(int $id): User
    {
        $user = User::find()
            ->where(['id' => $id, 'deleted_at' => null])
            ->one();

        if (!$user instanceof User) {
            throw new NotFoundHttpException(Yii::t('user', 'User not found.'));
        }

        return $user;
    }

    /**
     * Получить список пользователей.
     *
     * @return ActiveDataProvider
     */
    public function getList(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => User::find()
                ->where(['deleted_at' => null])
                ->orderBy(['id' => SORT_ASC]),
            'pagination' => [
                'defaultPageSize' => 20,
                'pageSizeLimit' => [1, 100],
                'pageParam' => 'page',
                'pageSizeParam' => 'perPage',
            ],
        ]);
    }

    /**
     * Обновить пользователя.
     *
     * @param int $id
     * @param UserForm $form
     * @return User
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function update(int $id, UserForm $form): User
    {
        $user = $this->get($id);

        if ($form->hasField('fullName')) {
            $user->setAttribute('full_name', $form->fullName);
        }
        if ($form->hasField('phone')) {
            $user->phone = $form->phone === null ? null : (string) $form->phone;
        }

        if (!$user->save(false)) {
            throw new ServerErrorHttpException(Yii::t('user', 'Failed to update user.'));
        }
        $user->refresh();

        return $user;
    }

    /**
     * Удалить пользователя.
     *
     * @param int $id
     * @return void
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function delete(int $id): void
    {
        $user = $this->get($id);
        $user->setAttribute('deleted_at', new Expression('CURRENT_TIMESTAMP'));

        if (!$user->save(false)) {
            throw new ServerErrorHttpException(Yii::t('user', 'Failed to delete user.'));
        }
    }
}
