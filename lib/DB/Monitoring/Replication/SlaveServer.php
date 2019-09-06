<?php

namespace Ridibooks\Platform\Common\DB\Monitoring\Replication;

/**
 * 모니터링할 Slave 서버 관련 정보
 */
class SlaveServer
{
    /**
     * 모니터링할 Slave 커넥션 명. 로드 밸런싱을 거치지 않은 실제 MySQL 호스트의 커넥션 명을 입력.
     * @var string
     */
    public $connection_name;
    /**
     * Master 서버의 커넥션 명. 바이너리 로그를 조회하기 위해 사용.
     * @var string
     */
    public $master_connection_name;
    /**
     * Seconds_Behind_Master 값을 확인해서 설정값 이하면 안정적인 상태로 간주. 단위 초.
     * @var int
     */
    public $threshold_replication_lag;

    public function __construct($connection_name, $master_connection_name, $threshold_replication_lag)
    {
        $this->connection_name = $connection_name;
        $this->master_connection_name = $master_connection_name;
        $this->threshold_replication_lag = $threshold_replication_lag;
    }
}
