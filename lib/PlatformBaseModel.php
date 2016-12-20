<?php
namespace Ridibooks\Platform\Common;

use Gnf\db\base;
use Ridibooks\Library\DB\ConnectionProvider;
use Ridibooks\Library\DB\GnfConnectionProvider;

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
			$db = GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_WRITE);
		}

		return new static($db);
	}

	/**
	 * @return static
	 */
	public static function createRead()
	{
		return self::create(GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_READ));
	}

	/**
	 * @return static
	 */
	public static function createPlatformOnlyWrite()
	{
		return self::create(GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_ONLY_DB_WRITE));
	}

	/**
	 * @return static
	 */
	public static function createPlatformOnlyRead()
	{
		return self::create(GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_ONLY_DB_READ));
	}

	/**
	 * @return static
	 */
	public static function createCpstatWrite()
	{
		return self::create(GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_CP_STATISTICS));
	}

	/**
	 * @return static
	 */
	public static function createCpstatRead()
	{
		return self::create(GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_CP_STATISTICS_READ));
	}

	public function transactional(callable $callable)
	{
		return $this->db->transactional($callable);
	}
}
