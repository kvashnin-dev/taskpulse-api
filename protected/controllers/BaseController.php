<?php

declare(strict_types=1);

namespace app\controllers;

use app\support\ApiResponse;
use stdClass;
use yii\rest\Controller;

abstract class BaseController extends Controller
{
    /**
     * Authentication and rate limiting are enabled in their dedicated iterations.
     *
     * @return array<string, mixed>
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator'], $behaviors['rateLimiter']);

        return $behaviors;
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{data: mixed, meta: array<string, mixed>|stdClass}
     */
    protected function respond(mixed $data, array $meta = [], int $statusCode = 200): array
    {
        $this->response->setStatusCode($statusCode);

        return ApiResponse::success($data, $meta);
    }

    /**
     * @param array<string, list<string>> $fields
     * @return array{error: array{code: string, message: string, fields?: array<string, list<string>>}}
     */
    protected function respondWithError(
        string $code,
        string $message,
        int $statusCode,
        array $fields = [],
    ): array {
        $this->response->setStatusCode($statusCode);

        return ApiResponse::error($code, $message, $fields);
    }
}
