<?php

namespace Ridibooks\Platform\Common\DB;

use Doctrine\DBAL\Connection;
use Gnf\db\base;
use Gnf\db\PDO;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Ridibooks\Platform\Common\Cache\AdaptableCache;

class GnfConnectionProvider
{
    /**
     * @var \Doctrine\DBAL\Connection[]
     */
    private static $connection_pool = [];

    /**
     * @param $group_name
     *
     * @return base
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnection($group_name)
    {
        if (!isset(self::$connection_pool[$group_name])) {
            self::$connection_pool[$group_name] = self::createConnection($group_name);
        }

        return new PDO(self::$connection_pool[$group_name]);
    }

    /**
     * @param      $group_name
     * @param bool $is_auto_reconnect
     *
     * @return Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnectionWithAutoReconnection($group_name, $is_auto_reconnect = false)
    {
        if (!isset(self::$connection_pool[$group_name])) {
            self::$connection_pool[$group_name] = self::createConnection($group_name);
        }

        $connection = self::$connection_pool[$group_name];

        // 접속 보장을 해야하는 경우 핑을 확인
        if ($is_auto_reconnect && $connection->ping() === false) {
            $connection->close();
            $connection->connect();
        }

        return $connection;
    }

    /**
     * @param $group_name
     *
     * @return Connection
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    private static function createConnection($group_name)
    {
        $config = new Configuration();

        $connection = DriverManager::getConnection(\Config::getConnectionParams($group_name), $config);
        $connection->getConfiguration()->setResultCacheImpl(new AdaptableCache());
        $connection->setFetchMode(\PDO::FETCH_OBJ);

        return $connection;
    }

    public static function closeConnection($group_name)
    {
        if (array_key_exists($group_name, self::$connection_pool)) {
            $connection = self::$connection_pool[$group_name];
            /** @var $db Connection */
            $db = $connection->getDb();
            $db->close();
            unset(self::$connection_pool[$group_name]);
        }
    }

    public static function closeAllConnections()
    {
        foreach (self::$connection_pool as $group_name => $connection) {
            self::closeConnection($group_name);
        }
    }
}
