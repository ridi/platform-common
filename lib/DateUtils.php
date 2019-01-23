<?php

namespace Ridibooks\Platform\Common;

class DateUtils
{
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DEFAULT_EMPTY_DATETIME = '0000-00-00 00:00:00';
    const DEFAULT_INFINITY_DATETIME = '9999-12-31 23:59:59';
    const DAYS_KR_SET = ['월', '화', '수', '목', '금', '토', '일'];

    /**
     * @param \DateTime $datetime
     *
     * @return int
     */
    public static function getWeekNumberOfMonth($datetime)
    {
        $first_day_of_month = clone $datetime;
        $first_day_of_month->modify('first day of this month');

        return ceil($datetime->format('W') - $first_day_of_month->format('W')) + 1;
    }

    /**
     * 입력받은 양식에 맞춘 날자를 반환한다.
     *
     * @param $date
     * @param $format
     *
     * @return bool|string
     */
    public static function getFormattedDate($date, $format)
    {
        if (StringUtils::isEmpty($date) || StringUtils::isEmpty($format)) {
            return null;
        }

        return date($format, strtotime($date));
    }


    /**
     * @param $year_month /YYYY-mm
     *
     * @return string
     */
    public static function getPrevYearmonth($year_month)
    {
        $datetime = date_create($year_month . '-01');
        $datetime->modify('first day of last month');

        return $datetime->format('Y-m');
    }

    /**
     * @param $year_month /YYYY-mm
     *
     * @return string
     */
    public static function getNextYearmonth($year_month)
    {
        $datetime = date_create($year_month . '-01');
        $datetime->modify('first day of next month');

        return $datetime->format('Y-m');
    }

    /**
     * @param $year_month
     *
     * @return \DateTime
     */
    public static function getFirstdayOfYearmonth($year_month)
    {
        return $first_date = date_create($year_month . '-01');
    }

    /**
     * @param $year_month
     *
     * @return \DateTime
     */
    public static function getLastdayOfYearmonth($year_month)
    {
        $first_date = date_create($year_month . '-01');
        $last_date = clone $first_date;

        return $last_date->modify('last day of');
    }

    public static function isToday(\DateTime $last_executed_datetime): bool
    {
        $last_date = $last_executed_datetime->format('Y/m/d');
        $today_date = date('Y/m/d');
        if ($last_date === $today_date) {
            return true;
        }

        return false;
    }

    /**
     * @param string $date_or_date_time Y-m-d or Y-m-d H:i:s
     *
     * @return \DateTime
     */
    public static function convertToStartDateTime(string $date_or_date_time): \DateTime
    {
        if (strlen($date_or_date_time) <= 10) {
            return (new \DateTime($date_or_date_time))->setTime(0, 0);
        } else {
            return (new \DateTime($date_or_date_time));
        }
    }

    /**
     * @param string $date_or_date_time Y-m-d or Y-m-d H:i:s
     *
     * @return \DateTime
     */
    public static function convertToEndDateTime(string $date_or_date_time): \DateTime
    {
        if (strlen($date_or_date_time) <= 10) {
            return (new \DateTime($date_or_date_time))->setTime(23, 59, 59);
        } else {
            return (new \DateTime($date_or_date_time));
        }
    }

    /**
     * @param \DateTime $last_executed_datetime
     * @param array     $time_table
     *
     * @return bool
     */
    public static function isExecutionTimeInTimeTable(\DateTime $last_executed_datetime, array $time_table): bool
    {
        $now = self::createNowExceptMicroseconds();
        $execution_time = '';

        foreach ($time_table as $time) {
            if ($last_executed_datetime < $time) {
                $execution_time = $time;
                break;
            }
        }

        if (empty($execution_time) || $now < $execution_time) {
            return false;
        }

        return true;
    }

    public static function validateDateTime(string $datetime, string $format = 'Y-m-d H:i:s')
    {
        $date = \DateTime::createFromFormat($format, $datetime);

        return ($date !== false) && ($date->format($format) === $datetime);
    }

    public static function createNowExceptMicroseconds(): \DateTime
    {
        $date_time = new \DateTime();
        $timestamp = $date_time->getTimestamp();
        $date_time->setTimestamp($timestamp);

        return $date_time;
    }

    public static function convertDateDiffToKoreanUnit(string $start_date, string $end_date): string
    {
        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);
        $diff_days = (int)$start_date->diff($end_date)->days;

        if ($diff_days < 7) {
            return $diff_days . '일';
        }

        $korean_unit = round($diff_days / 7) . '주';

        if ($diff_days % 7 !== 0) {
            $korean_unit = '약 ' . $korean_unit;
        }

        return $korean_unit;
    }

    public static function fixInsertedWrongTime(string $time): string
    {
        $time_set = explode(':', $time);
        if (count($time_set) > 3) {
            $time_set = array_slice($time_set, 0, 3);
        }

        return implode(':', $time_set);
    }

    /**
     * 입력받은 이전 날자 가져온다.
     *
     * @param $day
     *
     * @return int|false
     */
    public static function getPreviousDay($day)
    {
        return strtotime($day . " days ago");
    }

    /**
     * @param string $check_date
     * @param string $start_date
     * @param string $end_date
     * @return bool
     * @throws \Exception
     */
    public static function checkInPeriod(string $check_date, string $start_date, string $end_date): bool
    {
        // FIXME : start_date만 있을때는 start_date보다 큰지, end_date만 있을때는 end_date보다 작은지의 부분적 비교도 가능하도록

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $check_ts = strtotime($check_date);

        if (!$start_ts) {
            throw new \Exception('start_date is not valid datetime string');
        }

        if (!$end_ts) {
            throw new \Exception('end_date is not valid datetime string');
        }

        if (!$check_ts) {
            throw new \Exception('check_date is not valid datetime string');
        }

        return (($check_ts >= $start_ts) && ($check_ts <= $end_ts));
    }

    public static function normalizeDateTimeString(string $datetime)
    {
        // 0000.00.00 => 0000-00-00
        $datetime = str_replace('.', '-', $datetime);

        // 0000-00.00  00:00:00 => 0000-00-00 00:00:00
        $datetime = str_replace('  ', ' ', $datetime);

        // 0000-00-00 00:00:00 AM => 0000-00-00 00:00:00
        $datetime = implode(' ', array_slice(explode(' ', $datetime), 0, 2));

        return $datetime;
    }
}
