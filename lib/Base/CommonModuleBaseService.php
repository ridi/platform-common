<?php
namespace Ridibooks\Platform\Common\Base;

use Ridibooks\Platform\Common\Constant\PlatformConnectionGroup;

abstract class CommonModuleBaseService
{
	/** @var string */
	protected $connection_group_name;

	public static function getMasterInstance()
	{
		$class_name = get_called_class();

		return new $class_name(PlatformConnectionGroup::COMMON_MODULE_READ_MASTER);
	}

	public static function getDefaultInstance()
	{
		$class_name = get_called_class();

		return new $class_name(PlatformConnectionGroup::COMMON_MODULE_READ);
	}

	public function __construct(string $connection_group_name)
	{
		$this->connection_group_name = $connection_group_name;
	}
}
