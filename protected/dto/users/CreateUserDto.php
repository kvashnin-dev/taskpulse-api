<?php

declare(strict_types=1);

namespace app\dto\users;

use yii\base\Model;

final class CreateUserDto extends Model
{
    public mixed $full_name = null;
    public mixed $phone = null;

    /**
     * @return array<int, mixed>
     */
    public function rules(): array
    {
        return [
            [
                'full_name',
                'filter',
                'filter' => static fn(mixed $value): mixed => is_string($value) ? trim($value) : $value,
            ],
            ['full_name', 'required', 'message' => 'Необходимо указать имя.'],
            [
                'full_name',
                'string',
                'min' => 3,
                'max' => 100,
                'message' => 'Имя должно быть строкой.',
                'tooShort' => 'Имя должно содержать не менее 3 символов.',
                'tooLong' => 'Имя должно содержать не более 100 символов.',
            ],
            [
                'phone',
                'string',
                'min' => 10,
                'max' => 15,
                'message' => 'Телефон должен быть строкой.',
                'tooShort' => 'Телефон должен содержать не менее 10 символов.',
                'tooLong' => 'Телефон должен содержать не более 15 символов.',
            ],
            ['phone', 'match', 'pattern' => '/^\+\d{9,14}$/', 'message' => 'Телефон должен начинаться с + и содержать только цифры.'],
        ];
    }
}
