<?php

declare(strict_types=1);

namespace tests\unit;

use app\services\HealthCheck\HealthCheckService;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use yii\db\Command;
use yii\db\Connection;

final class HealthCheckServiceTest extends TestCase
{
    public function testCheckReportsHealthyPostgres(): void
    {
        $command = $this->createMock(Command::class);
        $command->expects(self::once())
            ->method('queryScalar')
            ->willReturn('1');

        $db = $this->createMock(Connection::class);
        $db->expects(self::once())
            ->method('createCommand')
            ->with('SELECT 1')
            ->willReturn($command);

        self::assertSame(
            [
                'status' => 'ok',
                'services' => [
                    'app' => 'ok',
                    'postgres' => 'ok',
                ],
            ],
            (new HealthCheckService($db))->check(),
        );
    }

    public function testCheckReportsUnavailablePostgres(): void
    {
        $db = $this->createMock(Connection::class);
        $db->expects(self::once())
            ->method('createCommand')
            ->with('SELECT 1')
            ->willThrowException(new RuntimeException('Database is unavailable.'));

        self::assertSame(
            [
                'status' => 'error',
                'services' => [
                    'app' => 'ok',
                    'postgres' => 'error',
                ],
            ],
            (new HealthCheckService($db))->check(),
        );
    }
}
