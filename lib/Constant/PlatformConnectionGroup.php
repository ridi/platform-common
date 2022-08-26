<?php

namespace Ridibooks\Platform\Common\Constant;

abstract class PlatformConnectionGroup
{
    public const WRITE = 'write';
    public const PLATFORM_WRITE = 'bom_platform_write';
    public const PLATFORM_READ = 'bom_platform_read';
    /** @depecated */
    public const PLATFORM_SLAVE = 'bom_platform_slave';

    public const PLATFORM_ONLY_DB_WRITE = 'platform_only_write';
    public const PLATFORM_ONLY_DB_READ = 'platform_only_read';
    /** @depecated */
    public const PLATFORM_ONLY_DB_SLAVE = 'platform_only_slave';

    public const PLATFORM_BOOK_DB_WRITE = 'platform_book_write';
    public const PLATFORM_BOOK_DB_READ = 'platform_book_read';
    /** @depecated */
    public const PLATFORM_BOOK_DB_SLAVE = 'platform_book_slave';

    public const CP_STATISTICS = 'cp_statistics';
    public const CP_STATISTICS_READ = 'cp_statistics_read';
    /** @depecated */
    public const CP_STATISTICS_SLAVE = 'cp_statistics_slave';

    public const ACCOUNT_READ = 'account_read';
    public const LOG = 'log';
    public const LOG_READ = 'log_read';

    /** @depecated */
    public const BINLOG_WRITE = 'binlog_write';
    /** @depecated */
    public const BINLOG_READ = 'binlog_read';
    /** @depecated */
    public const BINLOG_SLAVE = 'binlog_slave';

    /** @depecated */
    public const COMMON_MODULE_READ_MASTER = 'common_module_read_master';
    /** @depecated */
    public const COMMON_MODULE_READ = 'common_module_read';
}
