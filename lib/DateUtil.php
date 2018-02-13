<?php

namespace Ridibooks\Platform\Common;

/**
 * @deprecated
 */
class DateUtil
{
    public static function isWeekend($date)
    {
        return date('N', strtotime($date)) >= 6;
    }
}
