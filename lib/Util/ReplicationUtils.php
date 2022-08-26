<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Util;

use Ridibooks\Platform\Common\DB\Monitoring\Replication\ReplicationStatusWatcher;
use Ridibooks\Platform\Common\DB\Monitoring\Replication\SlaveServer;

class ReplicationUtils
{
    public static function isStableBetweenSlaveAndMaster(
        string $slave_connection,
        string $master_connection,
        int $threshold_replication_lag
    ): bool {
        $slave_server = new SlaveServer($slave_connection, $master_connection, $threshold_replication_lag);

        return self::isReplicationServerStable($slave_server);
    }

    private static function isReplicationServerStable(SlaveServer $slave_server): bool
    {
        $replication_status_watcher = new ReplicationStatusWatcher([$slave_server]);

        return $replication_status_watcher->isStable();
    }
}
