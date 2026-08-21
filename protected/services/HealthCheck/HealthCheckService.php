<?php

declare(strict_types=1);

namespace app\services\HealthCheck;

use Throwable;
use yii\db\Connection;

final readonly class HealthCheckService
{
    public function __construct(private Connection $db) {}

    /**
     * @return array{status: 'ok'|'error', services: array{app: 'ok', postgres: 'ok'|'error'}}
     */
    public function check(): array
    {
        $postgresStatus = $this->checkPostgres() ? 'ok' : 'error';

        return [
            'status' => $postgresStatus === 'ok' ? 'ok' : 'error',
            'services' => [
                'app' => 'ok',
                'postgres' => $postgresStatus,
            ],
        ];
    }

    private function checkPostgres(): bool
    {
        try {
            return (int) $this->db->createCommand('SELECT 1')->queryScalar() === 1;
        } catch (Throwable) {
            return false;
        }
    }
}
