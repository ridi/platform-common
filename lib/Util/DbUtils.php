<?php

namespace Ridibooks\Platform\Common\Util;

use Doctrine\DBAL\Exception\DriverException;
use Gnf\db\base;
use Ridibooks\Platform\Common\Constant\PlatformConnectionGroup;
use Ridibooks\Platform\Common\DB\GnfConnectionProvider;

class DbUtils
{
    // http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
    // Error: 1205 SQLSTATE: HY000 (ER_LOCK_WAIT_TIMEOUT)
    // Error: 1213 SQLSTATE: 40001 (ER_LOCK_DEADLOCK)
    // Error: 1614 SQLSTATE: XA102 (ER_XA_RBDEADLOCK)
    // Error: 3058 SQLSTATE: HY000 (ER_USER_LOCK_DEADLOCK)
    // Error: 3132 SQLSTATE: HY000 (ER_LOCKING_SERVICE_DEADLOCK)
    public const DEADLOCK_ERROR_CODES = [1205, 1213, 1614, 3058, 3132];

    // http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
    // Error: 1062; SQLSTATE: 23000 (ER_DUP_ENTRY)
    public const DUPLICATE_ENTRY_ERROR_CODES = [1062];

    /**
     * @param      $function
     * @param      $db
     * @param int  $retry_count
     * @param int  $retry_gap_sleep_second
     * @param bool $transaction_on
     *
     * @return bool
     * @throws DriverException
     * @throws \Exception
     */
    public static function queryRetryOnLock(
        $function,
        $db = null,
        $retry_count = 5,
        $retry_gap_sleep_second = 0,
        $transaction_on = true
    ) {
        // http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
        // Error: 1205 SQLSTATE: HY000 (ER_LOCK_WAIT_TIMEOUT)
        return self::queryRetry($function, $db, $retry_count, $retry_gap_sleep_second, $transaction_on, [1205]);
    }

    /**
     * @param      $function
     * @param      $db
     * @param int  $retry_count
     * @param int  $retry_gap_sleep_second
     * @param bool $transaction_on
     *
     * @return bool
     * @throws DriverException
     * @throws \Exception
     */
    public static function queryRetryOnLockDeadlock(
        $function,
        $db = null,
        $retry_count = 5,
        $retry_gap_sleep_second = 0,
        $transaction_on = true
    ) {
        return self::queryRetry(
            $function,
            $db,
            $retry_count,
            $retry_gap_sleep_second,
            $transaction_on,
            self::DEADLOCK_ERROR_CODES
        );
    }

    /**
     * @param      $function
     * @param      $db
     * @param int  $retry_count
     * @param int  $retry_gap_sleep_second
     * @param bool $transaction_on
     *
     * @return bool
     * @throws DriverException
     * @throws \Exception
     */
    public static function queryRetryOnDuplicateEntry(
        $function,
        $db = null,
        $retry_count = 5,
        $retry_gap_sleep_second = 0,
        $transaction_on = true
    ) {
        return self::queryRetry(
            $function,
            $db,
            $retry_count,
            $retry_gap_sleep_second,
            $transaction_on,
            self::DUPLICATE_ENTRY_ERROR_CODES
        );
    }

    /**
     * @param       $function
     * @param       $db
     * @param int   $retry_count
     * @param int   $retry_gap_sleep_second
     * @param bool  $transaction_on
     *
     * @param array $error_codes http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
     *
     * @return bool
     * @throws DriverException
     * @throws \Exception
     */
    private static function queryRetry(
        $function,
        $db = null,
        $retry_count = 5,
        $retry_gap_sleep_second = 0,
        $transaction_on = true,
        $error_codes = []
    ) {
        if (!is_callable($function)) {
            throw new \InvalidArgumentException(
                'Expected argument of type "callable", got "' . gettype($function) . '"'
            );
        }

        if (is_null($db)) {
            $db = GnfConnectionProvider::getConnection(PlatformConnectionGroup::PLATFORM_WRITE);
        }

        $tries = 0;
        do {
            try {
                if ($transaction_on) {
                    return $db->transactional($function);
                } else {
                    $function();
                }

                return true;
            } catch (DriverException $driver_exception) {
                if (in_array($driver_exception->getErrorCode(), $error_codes)) {
                    $tries++;
                    if ($retry_gap_sleep_second) {
                        sleep($retry_gap_sleep_second);
                    }
                    if ($tries == $retry_count) {
                        throw $driver_exception;
                    }
                } else {
                    throw $driver_exception;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        } while ($tries < $retry_count);

        return true;
    }

    /**
     * @param callable $callback
     * @param          $db_1st
     * @param          $db_2nd
     * @param int      $retry_count
     * @param int      $sleep_seconds
     * @param bool     $transaction_on
     *
     * @return bool
     */
    public static function multiDbQueryRetryOnLockDeadlock(
        callable $callback,
        $db_1st,
        $db_2nd,
        int $retry_count = 5,
        int $sleep_seconds = 0,
        bool $transaction_on = true
    ): bool {
        $function_1st = function () use ($db_2nd, $callback, $retry_count, $sleep_seconds, $transaction_on) {
            $function_2nd = function () use ($callback) {
                $callback();
            };
            DbUtils::queryRetryOnLockDeadlock($function_2nd, $db_2nd, $retry_count, $sleep_seconds, $transaction_on);
        };

        return DbUtils::queryRetryOnLockDeadlock($function_1st, $db_1st, $retry_count, $sleep_seconds, $transaction_on);
    }

    /**
     * 임시로 safe_update 없이 실행. 긴급시에만 대처 후 쿼리 수정해야함.
     *
     * @param base     $db
     * @param callable $callback
     *
     * @throws \Throwable
     */
    public static function executeWithoutSafeUpdate(base $db, callable $callback)
    {
        $db->sqlDo('SET SQL_SAFE_UPDATES =0');
        try {
            $callback();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $db->sqlDo('SET SQL_SAFE_UPDATES =1');
        }
    }

    /**
     * @param array    $items
     * @param callable $callable
     * @param int      $split_size
     *
     * @return array Stacked result from callable
     */
    public static function executeWithSplitItems(array $items, callable $callable, int $split_size = 100): array
    {
        $result_set = [];
        $item_count = count($items);

        for ($offset = 0; $offset < $item_count; $offset += $split_size) {
            $result_set[] = $callable(array_slice($items, $offset, $split_size));
        }

        return array_flatten($result_set); // FIXME not use laravel/helper
    }

    /**
     * GnfDB 의 sqlAnd 기능 보강. 같은 컬럼에 여러 조건을 비교했을 때 정상 작동하도록
     *
     * @param string          $column
     * @param array|int|mixed $conditions
     *
     * @return \Gnf\db\Helper\GnfSqlAnd
     */
    public static function sameColumnAndCondition(string $column, $conditions)
    {
        if (!is_array($conditions)) {
            return sqlAnd([$column => $conditions]);
        }

        $conds = [];
        foreach ($conditions as $condition) {
            $conds[] = [$column => $condition];
        }

        // GnfDB 의 sqlAnd 는 여러 조건의 매개변수를 array 로 주면 안되고 arguments 로 넣어줘야 한다.
        return call_user_func_array('sqlAnd', $conds);
    }
}
