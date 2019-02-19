<?php
namespace Ridibooks\Platform\Common;

use Gnf\db\base;
use Ridibooks\Library\DB\GnfConnectionProvider;
use Ridibooks\Platform\Common\Constant\PlatformConnectionGroup;

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
     * @param base $db
     *
     * @return static
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
     */
    public static function createRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_READ));
    }

    /**
     * @return static
     */
    public static function createSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_SLAVE));
    }

    /**
     * @return static
     */
    public static function createPlatformOnlyWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_WRITE));
    }

    /**
     * @return static
     */
    public static function createPlatformOnlyRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_READ));
    }

    /**
     * @return static
     */
    public static function createPlatformOnlySlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_ONLY_DB_SLAVE));
    }

    /**
     * @return static
     */
    public static function createPlatformBookWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_WRITE));
    }

    /**
     * @return static
     */
    public static function createPlatformBookRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_READ));
    }

    /**
     * @return static
     */
    public static function createPlatformBookSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_BOOK_DB_SLAVE));
    }

    /**
     * @return static
     */
    public static function createCpstatWrite()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS));
    }

    /**
     * @return static
     */
    public static function createCpstatRead()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS_READ));
    }

    /**
     * @return static
     */
    public static function createCpstatSlave()
    {
        return self::create(GnfConnectionProvider::getConnection(PlatformConnectionGroup::CP_STATISTICS_SLAVE));
    }

    public function transactional(callable $callable)
    {
        return $this->db->transactional($callable);
    }
}
