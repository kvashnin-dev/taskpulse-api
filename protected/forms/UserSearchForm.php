<?php

declare(strict_types=1);

namespace app\forms;

use yii\base\Model;

final class UserSearchForm extends Model
{
    public mixed $page = 1;
    public mixed $per_page = 20;

    /**
     * @return array<int, mixed>
     */
    public function rules(): array
    {
        return [
            [
                'page',
                'integer',
                'min' => 1,
                'message' => 'Номер страницы должен быть целым числом.',
                'tooSmall' => 'Номер страницы должен быть не меньше 1.',
            ],
            [
                'per_page',
                'integer',
                'min' => 1,
                'max' => 100,
                'message' => 'Размер страницы должен быть целым числом.',
                'tooSmall' => 'Размер страницы должен быть не меньше 1.',
                'tooBig' => 'Размер страницы должен быть не больше 100.',
            ],
        ];
    }
}
