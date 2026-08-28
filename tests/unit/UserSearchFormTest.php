<?php

declare(strict_types=1);

namespace tests\unit;

use app\modules\user\forms\UserSearchForm;
use PHPUnit\Framework\TestCase;

final class UserSearchFormTest extends TestCase
{
    public function testDefaultPagination(): void
    {
        $form = new UserSearchForm();

        self::assertTrue($form->validate());
        self::assertSame(1, $form->page);
        self::assertSame(20, $form->perPage);
    }

    public function testPaginationValidation(): void
    {
        $form = new UserSearchForm();
        $form->load([
            'page' => 'wrong',
            'perPage' => 101,
        ], '');

        self::assertFalse($form->validate());
        self::assertSame(
            'Номер страницы должен быть целым числом.',
            $form->getFirstError('page'),
        );
        self::assertSame(
            'Размер страницы должен быть не больше 100.',
            $form->getFirstError('perPage'),
        );
    }
}
