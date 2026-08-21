<?php

declare(strict_types=1);

namespace app\controllers;

use app\services\HealthCheck\HealthCheckService;
use LogicException;
use stdClass;
use Yii;
use yii\db\Connection;

final class HealthController extends BaseController
{
    /**
     * @return array<string, list<string>>
     */
    protected function verbs(): array
    {
        return [
            'index' => ['GET'],
        ];
    }

    /**
     * @return array{data: mixed, meta: array<string, mixed>|stdClass}
     */
    public function actionIndex(): array
    {
        $db = Yii::$app->get('db');
        if (!$db instanceof Connection) {
            throw new LogicException('Database component is not configured.');
        }

        $health = (new HealthCheckService($db))->check();
        $statusCode = $health['status'] === 'ok' ? 200 : 503;

        return $this->respond($health, statusCode: $statusCode);
    }
}
