<?php
namespace Ridibooks\Platform\Common\Constant;

abstract class PlatformConnectionGroup
{
    const PLATFORM_WRITE = 'bom_platform_write';
    const PLATFORM_READ = 'bom_platform_read';
    const PLATFORM_SLAVE = 'bom_platform_slave';

    const PLATFORM_ONLY_DB_WRITE = 'platform_only_write';
    const PLATFORM_ONLY_DB_READ = 'platform_only_read';
    const PLATFORM_ONLY_DB_SLAVE = 'platform_only_slave';

    const PLATFORM_BOOK_DB_WRITE = 'platform_book_write';
    const PLATFORM_BOOK_DB_READ = 'platform_book_read';
    const PLATFORM_BOOK_DB_SLAVE = 'platform_book_slave';

    const CP_STATISTICS = 'cp_statistics';
    const CP_STATISTICS_READ = 'cp_statistics_read';
    const CP_STATISTICS_SLAVE = 'cp_statistics_slave';

    const COMMON_MODULE_READ_MASTER = 'common_module_read_master';
    const COMMON_MODULE_READ = 'common_module_read';
}
