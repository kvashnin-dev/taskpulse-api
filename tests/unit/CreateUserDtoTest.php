<?php

declare(strict_types=1);

namespace tests\unit;

use app\dto\users\CreateUserDto;
use PHPUnit\Framework\TestCase;

final class CreateUserDtoTest extends TestCase
{
    public function testValidData(): void
    {
        $dto = CreateUserDto::fromArray([
            'full_name' => 'Иван Петров',
            'phone' => '+79991234567',
        ]);

        self::assertTrue($dto->validate());
    }

    public function testNameIsRequired(): void
    {
        $dto = CreateUserDto::fromArray(['phone' => '+79991234567']);

        self::assertFalse($dto->validate());
        self::assertSame('Необходимо указать имя.', $dto->getFirstError('full_name'));
    }

    public function testPhoneFormat(): void
    {
        $dto = CreateUserDto::fromArray([
            'full_name' => 'Иван Петров',
            'phone' => '89991234567',
        ]);

        self::assertFalse($dto->validate());
        self::assertSame(
            'Телефон должен начинаться с + и содержать только цифры.',
            $dto->getFirstError('phone'),
        );
    }

    public function testNameIsTrimmedBeforeValidation(): void
    {
        $dto = CreateUserDto::fromArray(['full_name' => ' И ']);

        self::assertFalse($dto->validate());
        self::assertSame('И', $dto->full_name);
        self::assertSame(
            'Имя должно содержать не менее 3 символов.',
            $dto->getFirstError('full_name'),
        );
    }

    public function testRejectsNonStringValues(): void
    {
        $dto = CreateUserDto::fromArray([
            'full_name' => 123,
            'phone' => 79991234567,
        ]);

        self::assertFalse($dto->validate());
        self::assertSame('Имя должно быть строкой.', $dto->getFirstError('full_name'));
        self::assertSame('Телефон должен быть строкой.', $dto->getFirstError('phone'));
    }
}
