<?php

declare(strict_types=1);

namespace app\support;

use stdClass;

final class ApiResponse
{
    /**
     * @param array<string, mixed> $meta
     * @return array{data: mixed, meta: array<string, mixed>|stdClass}
     */
    public static function success(mixed $data, array $meta = []): array
    {
        return [
            'data' => $data,
            'meta' => $meta === [] ? new stdClass() : $meta,
        ];
    }

    /**
     * @param array<string, list<string>> $fields
     * @return array{error: array{code: string, message: string, fields?: array<string, list<string>>}}
     */
    public static function error(string $code, string $message, array $fields = []): array
    {
        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if ($fields !== []) {
            $error['fields'] = $fields;
        }

        return ['error' => $error];
    }
}
