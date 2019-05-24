<?php

namespace Ridibooks\Platform\Common\DB\Monitoring\Replication;

use Ridibooks\Platform\Common\Constant\SlaveStatusConst;

/**
 * Slave 서버의 모니터링 결과
 */
class SlaveStatus
{
    /** @var string $server_name */
    public $server_name;
    /** @var string $status */
    public $status;
    /** @var array $raw_result */
    public $raw_result;
    /** @var null|string $error_msg */
    public $error_msg;

    /**
     * @param string $server_name
     * @param string $status
     * @param array $raw_result
     * @param null|string $error_msg
     * @throws \Exception
     */
    public function __construct($server_name, $status, $raw_result = [], $error_msg = null)
    {
        if (!in_array($status, SlaveStatusConst::$TYPES)) {
            throw new \Exception('Invalid status - ' . $status);
        }

        $this->server_name = $server_name;
        $this->status = $status;
        $this->raw_result = $raw_result;
        $this->error_msg = $error_msg;
    }
}
