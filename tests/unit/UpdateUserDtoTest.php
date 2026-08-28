<?php

declare(strict_types=1);

namespace tests\unit;

use app\dto\users\UpdateUserDto;
use PHPUnit\Framework\TestCase;

final class UpdateUserDtoTest extends TestCase
{
    public function testTracksProvidedFields(): void
    {
        $dto = new UpdateUserDto();
        $dto->load(['phone' => null], '');

        self::assertTrue($dto->hasChanges());
        self::assertTrue($dto->hasField('phone'));
        self::assertFalse($dto->hasField('full_name'));
        self::assertTrue($dto->validate());
    }

    public function testEmptyRequestHasNoChanges(): void
    {
        $dto = new UpdateUserDto();
        $dto->load([], '');

        self::assertFalse($dto->hasChanges());
    }

    public function testProvidedNameCannotBeEmpty(): void
    {
        $dto = new UpdateUserDto();
        $dto->load(['full_name' => ''], '');

        self::assertFalse($dto->validate());
        self::assertSame('Необходимо указать имя.', $dto->getFirstError('full_name'));
    }
}
