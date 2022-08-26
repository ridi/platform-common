<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Constant;

abstract class TimeConstant
{
    // Seconds in units of time

    public const SEC_IN_MINUTE = 60;
    public const SEC_IN_HOUR = 3600;
    public const SEC_IN_DAY = 86400;
    public const SEC_IN_WEEK = 604800;   // 7 days
    public const SEC_IN_MONTH = 2592000; // 30 days
    public const SEC_IN_YEAR = 31536000; // 365 days
}
