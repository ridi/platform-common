<?php

namespace Ridibooks\Platform\Common\Base;

use Ridibooks\Library\DB\ConnectionProvider;
use Ridibooks\Library\DB\GnfConnectionProvider;

class AdminBaseModel
{
	protected $db;
	protected $read_db;

	public function __construct()
	{
		$this->db = self::getDb();
		$this->read_db = self::getReadDb();
	}

	protected static function getDb()
	{
		return GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_WRITE);
	}

	protected static function getReadDb()
	{
		return GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_PLATFORM_READ);
	}

	protected static function getCpReadDb()
	{
		return GnfConnectionProvider::getConnection(ConnectionProvider::CONNECTION_GROUP_CP_STATISTICS_READ);
	}

	/**
	 * 트랜잭션 시작(모델이 아닌 서비스에서 명시적인 트랜잭션이 필요할 경우 사용)
	 * @deprecated use transactional
	 */
	public static function startTransaction()
	{
		$db = self::getDb();
		$db->sqlBegin();
	}

	/**
	 * 트랜잭션 종료(모델이 아닌 서비스에서 명시적인 트랜잭션이 필요할 경우 사용)
	 * @deprecated use transactional
	 */
	public static function endTransaction()
	{
		$db = self::getDb();
		return $db->sqlEnd();
	}

	/**
	 * 트랜잭셔널 (모델이 아닌 서비스에서 명시적인 트랜잭션이 필요할 경우 사용)
	 * @param $func callable
	 * @return bool
	 * @throws \Exception
	 */
	public static function transactional($func)
	{
		$db = self::getDb();
		return $db->transactional($func);
	}
}
