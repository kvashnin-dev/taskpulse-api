<?php

declare(strict_types=1);

namespace tests\unit;

use app\support\ApiResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ApiResponseTest extends TestCase
{
    public function testSuccessResponseContainsDataAndMeta(): void
    {
        self::assertSame(
            [
                'data' => ['id' => 42],
                'meta' => ['request_id' => 'test-request'],
            ],
            ApiResponse::success(['id' => 42], ['request_id' => 'test-request']),
        );
    }

    public function testSuccessResponseUsesJsonObjectForEmptyMeta(): void
    {
        self::assertEquals(
            [
                'data' => ['status' => 'ok'],
                'meta' => new stdClass(),
            ],
            ApiResponse::success(['status' => 'ok']),
        );
    }

    public function testErrorResponseOmitsEmptyFields(): void
    {
        self::assertSame(
            [
                'error' => [
                    'code' => 'not_found',
                    'message' => 'Resource not found',
                ],
            ],
            ApiResponse::error('not_found', 'Resource not found'),
        );
    }

    public function testValidationErrorContainsFields(): void
    {
        self::assertSame(
            [
                'error' => [
                    'code' => 'validation_error',
                    'message' => 'Validation failed',
                    'fields' => ['title' => ['Title cannot be blank.']],
                ],
            ],
            ApiResponse::error(
                'validation_error',
                'Validation failed',
                ['title' => ['Title cannot be blank.']],
            ),
        );
    }
}
