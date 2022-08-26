<?php

namespace Ridibooks\Platform\Common\Util;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sentry\Monolog\Handler;
use Sentry\SentrySdk;

class MonologHelper
{
    public const LOG_FILE_BASE_PATH = '/var/log/ridi/';
    public const LOG_FILE_NAME_EXTENSION = '.log';

    /**
     * @param string $name
     * @return Logger
     */
    public static function createForStdout($name)
    {
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler("php://stdout"));

        return $log;
    }

    /**
     * @param string $logger_name
     * @param null|string $file_name
     * @param bool $is_enabled_sentry_alert_when_error_occurred
     * @param bool $is_split_log_files_by_day
     * @return Logger
     * @throws \Exception
     */
    public static function createForLogFile(
        string $logger_name,
        $file_name = null,
        bool $is_enabled_sentry_alert_when_error_occurred = true,
        bool $is_split_log_files_by_day = false
    ) {
        if (empty($file_name)) {
            $file_name = $logger_name;
        }

        if ($is_split_log_files_by_day) {
            $file_name .= '_' . date('Ymd');
        }

        $file_name = self::LOG_FILE_BASE_PATH . $file_name . self::LOG_FILE_NAME_EXTENSION;

        $path_info = pathinfo($file_name);
        if (!file_exists($path_info['dirname'])) {
            if (!mkdir($path_info['dirname'], 0777, true)) {
                throw new \Exception('Failed to create log file directory');
            }
        }

        $log = new Logger($logger_name);
        $log->pushHandler(new StreamHandler($file_name));

        if ($is_enabled_sentry_alert_when_error_occurred) {
            $handler = new Handler(SentrySdk::getCurrentHub(), Logger::ERROR);
            $log->pushHandler($handler);
        }

        return $log;
    }

    /**
     * @param string $name
     * @param string|null $file_name optional file name
     * @return Logger
     */
    public static function createForCron($name, $file_name = null)
    {
        if (empty($file_name)) {
            $file_name = $name;
        }

        $file_name = 'cron_' . $file_name;

        $log = new Logger($name);
        $log->pushHandler(new StreamHandler("php://stdout"));
        $log->pushHandler(new StreamHandler(self::LOG_FILE_BASE_PATH . $file_name . self::LOG_FILE_NAME_EXTENSION));

        $handler = new Handler(SentrySdk::getCurrentHub(), Logger::ERROR);
        $log->pushHandler($handler);

        return $log;
    }

    /**
     * @param $pg_name
     * @param $ridi_tid
     * @return Logger
     */
    public static function createForPayment($pg_name, $ridi_tid)
    {
        $file_name = 'payment-' . strtolower(trim($pg_name));

        $log = new Logger('payment');
        $log->pushHandler(new RotatingFileHandler(self::LOG_FILE_BASE_PATH . $file_name . self::LOG_FILE_NAME_EXTENSION));
        $log->pushProcessor(
            function ($record) use ($ridi_tid) {
                $record['extra']['tid'] = $ridi_tid;

                return $record;
            }
        );

        return $log;
    }

    /**
     * @param string $description
     * @param array $count_map
     * @param Logger $logger
     */
    public static function loggerInfoSummary($logger, $description, $count_map)
    {
        $summary = $description;
        foreach ($count_map as $key => $value) {
            $summary .= ' / ' . $key . ':' . $value;
        }
        $logger->info($summary);
    }

    /**
     * @param array $det
     * @param array $from
     */
    public static function addCounterMap(&$det, $from)
    {
        foreach ($from as $key => $value) {
            $counter = &$det[$key];
            if (empty($counter)) {
                $counter = 0;
            }
            $counter += $value;
        }
    }

    /**
     * @param Logger $logger
     * @param int $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    public static function addRecordToLogger($logger, $level, $message, $context = [])
    {
        if ($logger != null) {
            return $logger->addRecord($level, $message, $context);
        }

        return false;
    }
}
