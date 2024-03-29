<?php

namespace Ridibooks\Platform\Common\Constant;

/**
 * Slave 서버의 모니터링 결과 상태값
 */
abstract class SlaveStatusConst
{
    public const STABLE = 'STABLE';			// 안정적
    public const WARNING = 'WARNING';			// Replication Lag 임계점 초과
    public const NOT_WORKING = 'NOT_WORKING';	// Slave 동작 안함
    public const NOT_SLAVE = 'NOT_SLAVE';		// Slave 아님
    public const ERROR = 'ERROR';				// 상태 조회 실패

    public static $TYPES = [
        self::STABLE,
        self::WARNING,
        self::NOT_WORKING,
        self::NOT_SLAVE,
        self::ERROR
    ];
}
