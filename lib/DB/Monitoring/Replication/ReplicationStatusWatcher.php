<?php

namespace Ridibooks\Platform\Common\DB\Monitoring\Replication;

use Ridibooks\Platform\Common\Constant\SlaveStatusConst;
use Ridibooks\Platform\Common\DB\GnfConnectionProvider;
use Ridibooks\Platform\Common\Exception\MsgException;

/**
 * SHOW SLAVE STATUS 결과로 Slave 서버들의 상태를 확인
 * http://dev.mysql.com/doc/refman/5.7/en/show-slave-status.html
 */
class ReplicationStatusWatcher
{
    /** @var SlaveServer[] */
    private $servers;

    /**
     * @param SlaveServer[] $servers 모니터링 할 Slave 서버 목록
     * @throws MsgException
     */
    public function __construct($servers)
    {
        if (count($servers) === 0) {
            throw new MsgException('Empty slave server list for replication status watcher.');
        }

        $this->servers = $servers;
    }

    /**
     * 모든 Slave 서버 정보
     * @return SlaveStatus[]
     * @throws \Exception
     */
    public function getTotalStatus()
    {
        $status = [];
        foreach ($this->servers as $server) {
            $status[] = $this->createSlaveStatus($server);
        }

        return $status;
    }

    /**
     * 모든 Slave 서버들의 상태값만 리턴
     *
     * @param SlaveStatus[]|null $total_status
     *
     * @return array [server_name => status, ...]
     * @throws \Exception
     */
    public function getEssentialStatusMap($total_status = null)
    {
        if ($total_status === null) {
            $total_status = $this->getTotalStatus();
        }

        $result = [];
        foreach ($total_status as $server_status) {
            $result[$server_status->server_name] = $server_status->status;
        }

        return $result;
    }

    /**
     * 모든 Slave 서버가 안정적인지 확인
     *
     * @param SlaveStatus[]|null $total_status
     *
     * @return bool
     * @throws \Exception
     */
    public function isStable($total_status = null)
    {
        if ($total_status === null) {
            $total_status = $this->getTotalStatus();
        }

        foreach ($total_status as $server_status) {
            if (!$this->isStableServer($server_status)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param SlaveStatus $slave_status
     * @return bool
     */
    public function isStableServer($slave_status)
    {
        if ($slave_status->status !== SlaveStatusConst::STABLE) {
            return false;
        }

        return true;
    }

    /**
     * @param SlaveServer $server 서버 정보
     *
     * @return SlaveStatus
     * @throws \Exception
     */
    private function createSlaveStatus($server)
    {
        $server_name = $server->connection_name;
        $error_msg = null;

        try {
            $raw_result = $this->getSlaveStatus($server_name);

            if (!empty($raw_result)
                && (!isset($raw_result['Slave_IO_Running']) || !isset($raw_result['Slave_SQL_Running']))
            ) {
                throw new MsgException("Slave status results hasn't following fields: "
                    . "Slave_IO_Running, Slave_SQL_Running, Seconds_Behind_Master.");
            }

            if (empty($raw_result)) {
                $status = SlaveStatusConst::NOT_SLAVE;
            } elseif ($raw_result['Slave_IO_Running'] !== 'Yes' || $raw_result['Slave_SQL_Running'] !== 'Yes') {
                $status = SlaveStatusConst::NOT_WORKING;
            } elseif (!isset($raw_result['Seconds_Behind_Master']) || is_nan($raw_result['Seconds_Behind_Master'])
                || intval($raw_result['Seconds_Behind_Master']) > $server->threshold_replication_lag
            ) {
                $status = SlaveStatusConst::WARNING;
            } else {
                $status = SlaveStatusConst::STABLE;
            }
        } catch (\Exception $e) {
            trigger_error($e->getMessage());

            $status = SlaveStatusConst::ERROR;
            $raw_result = [];
            $error_msg = $e->getMessage();
        }

        return new SlaveStatus($server_name, $status, $raw_result, $error_msg);
    }

    /**
     * @param string $connection_group_name
     *
     * @return array SHOW SLAVE STATUS 정보
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getSlaveStatus($connection_group_name)
    {
        $db = GnfConnectionProvider::getConnectionWithAutoReconnection($connection_group_name, true);
        $result = $db->fetchAssocAll('SHOW SLAVE STATUS');

        return empty($result) ? [] : $result[0];
    }

    /**
     * @param string $slave_server_name
     * @return string
     * @throws MsgException
     */
    public function getMasterServerName($slave_server_name)
    {
        foreach ($this->servers as $server) {
            if ($server->connection_name === $slave_server_name) {
                return $server->master_connection_name;
            }
        }

        throw new MsgException("Can't find master server name of given slave server name '{$slave_server_name}'");
    }
}
