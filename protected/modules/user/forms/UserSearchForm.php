<?php

declare(strict_types=1);

namespace app\modules\user\forms;

use Yii;
use yii\base\Model;

/**
 * Форма пагинации списка пользователей.
 */
final class UserSearchForm extends Model
{
    /** @var mixed Номер страницы. */
    public mixed $page = 1;

    /** @var mixed Размер страницы. */
    public mixed $perPage = 20;

    /**
     * @inheritDoc
     * @param array<string, mixed> $data
     */
    public function load($data, $formName = null): bool
    {
        return parent::load([
            'page' => $data['page'] ?? $this->page,
            'perPage' => $data['per_page'] ?? $this->perPage,
        ], '');
    }

    /**
     * @inheritDoc
     * @return array<int, mixed>
     */
    public function rules(): array
    {
        return [
            [
                'page',
                'integer',
                'min' => 1,
                'skipOnEmpty' => false,
                'message' => Yii::t('user', 'Page must be an integer.'),
                'tooSmall' => Yii::t('user', 'Page must be no less than 1.'),
            ],
            [
                'perPage',
                'integer',
                'min' => 1,
                'max' => 100,
                'skipOnEmpty' => false,
                'message' => Yii::t('user', 'Page size must be an integer.'),
                'tooSmall' => Yii::t('user', 'Page size must be no less than 1.'),
                'tooBig' => Yii::t('user', 'Page size must be no greater than 100.'),
            ],
        ];
    }

    /**
     * @inheritDoc
     * @return array<string, string>
     */
    public function getFirstErrors(): array
    {
        $errors = [];

        foreach (parent::getFirstErrors() as $attribute => $message) {
            $errors[$attribute === 'perPage' ? 'per_page' : $attribute] = $message;
        }

        return $errors;
    }
}
