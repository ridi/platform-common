<?php
namespace Ridibooks\Platform\Common\Base;

use Gnf\db\base;
use Ridibooks\Platform\Common\Constant\PlatformConnectionGroup;
use Ridibooks\Platform\Common\DB\GnfConnectionProvider;

abstract class PlatformBaseModel
{
    /**
     * @var base
     */
    protected $db;

    private function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param base|null $db
     *
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function create(base $db = null)
    {
        if ($db === null) {
            $db = GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_WRITE);
        }

        return new static($db);
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_READ));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_SLAVE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformOnlyWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_WRITE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformOnlyRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_READ));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformOnlySlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_SLAVE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformBookWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_WRITE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformBookRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_READ));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createPlatformBookSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_SLAVE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createCpstatWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createCpstatRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS_READ));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createCpstatSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS_SLAVE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createBinlogWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::BINLOG_WRITE));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createBinlogRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::BINLOG_READ));
    }

    /**
     * @return static
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function createBinlogSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::BINLOG_SLAVE));
    }

    /**
     * @param callable $callable
     *
     * @return bool
     * @throws \Exception
     */
    public function transactional(callable $callable)
    {
        return $this->db->transactional($callable);
    }
}
