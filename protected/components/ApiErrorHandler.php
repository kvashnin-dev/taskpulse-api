<?php

declare(strict_types=1);

namespace app\components;

use app\support\ApiResponse;
use Throwable;
use yii\web\ErrorHandler;
use yii\web\HttpException;

final class ApiErrorHandler extends ErrorHandler
{
    /**
     * @param Throwable $exception
     * @return array{error: array{code: string, message: string, fields?: array<string, list<string>>}}
     */
    protected function convertExceptionToArray($exception): array
    {
        $statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
        $code = match ($statusCode) {
            400 => 'bad_request',
            404 => 'not_found',
            405 => 'method_not_allowed',
            409 => 'conflict',
            422 => 'validation_error',
            429 => 'rate_limit_exceeded',
            503 => 'service_unavailable',
            default => 'internal_error',
        };
        $message = $statusCode < 500 ? $exception->getMessage() : 'Internal server error';

        return ApiResponse::error($code, $message);
    }
}
