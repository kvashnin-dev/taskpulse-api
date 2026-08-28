<?php

declare(strict_types=1);

namespace app\dto\users;

use yii\base\Model;

final class UpdateUserDto extends Model
{
    public mixed $full_name = null;
    public mixed $phone = null;

    /** @var list<string> */
    private array $providedFields = [];

    /**
     * @param array<string, mixed> $data
     */
    public function load($data, $formName = null): bool
    {
        $this->providedFields = array_values(
            array_intersect(['full_name', 'phone'], array_keys($data)),
        );

        return parent::load($data, $formName);
    }

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
                'skipOnEmpty' => false,
            ],
            ['full_name', 'validateChanges', 'skipOnEmpty' => false],
            ['full_name', 'validateFullName', 'skipOnEmpty' => false],
            [
                'full_name',
                'string',
                'min' => 3,
                'max' => 100,
                'skipOnEmpty' => true,
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

    public function hasChanges(): bool
    {
        return $this->providedFields !== [];
    }

    public function hasField(string $field): bool
    {
        return in_array($field, $this->providedFields, true);
    }

    public function validateFullName(): void
    {
        if (
            $this->hasField('full_name')
            && ($this->full_name === null || (is_string($this->full_name) && trim($this->full_name) === ''))
        ) {
            $this->addError('full_name', 'Необходимо указать имя.');
        }
    }

    public function validateChanges(): void
    {
        if (!$this->hasChanges()) {
            $this->addError('full_name', 'Не переданы данные для обновления.');
        }
    }
}
