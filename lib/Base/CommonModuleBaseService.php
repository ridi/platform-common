<?php
namespace Ridibooks\Platform\Common\Base;

use Ridibooks\Library\DB\GnfConnectionProvider;
use Ridibooks\Platform\Common\Constant\PlatformConnectionGroup;

abstract class CommonModuleBaseService
{
    /** @var \Gnf\db\base */
    protected $db;
    /** @var string */
    protected $connection_group_name;

    /** @return static */
    public static function getMasterInstance()
    {
        $class_name = get_called_class();

        return new $class_name(PlatformConnectionGroup::COMMON_MODULE_READ_MASTER);
    }

    /** @return static */
    public static function getDefaultInstance()
    {
        $class_name = get_called_class();

        return new $class_name(PlatformConnectionGroup::COMMON_MODULE_READ);
    }

    public function __construct(string $connection_group_name)
    {
        $this->connection_group_name = $connection_group_name;

        $this->db = GnfConnectionProvider::getConnection($connection_group_name);
    }

    public function closeConnection()
    {
        GnfConnectionProvider::closeConnection($this->connection_group_name);
    }
}
