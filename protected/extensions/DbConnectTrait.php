<?php

declare(strict_types=1);

namespace app\extensions;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * Подключение к базе данных.
 */
trait DbConnectTrait
{
    private ?Connection $dbConnection = null;

    /**
     * Получить подключение к базе данных.
     *
     * @return Connection
     * @throws InvalidConfigException
     */
    public function getDbConnection(): Connection
    {
        if (!$this->dbConnection instanceof Connection) {
            $db = Yii::$app->get('db', false);
            if (!$db instanceof Connection) {
                throw new InvalidConfigException('Компонент базы данных не настроен.');
            }

            $this->dbConnection = $db;
        }

        return $this->dbConnection;
    }

    /**
     * Установить подключение к базе данных.
     *
     * @param Connection $dbConnection
     * @return void
     */
    public function setDbConnection(Connection $dbConnection): void
    {
        $this->dbConnection = $dbConnection;
    }
}
