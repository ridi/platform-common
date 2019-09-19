<?php

namespace Ridibooks\Platform\Common\Util;

use Ridibooks\Platform\Common\Exception\MsgException;

class ExceptionUtils
{
    /**
     * Exception 처리한다.
     *
     * @param             $exception
     * @param null|string $msg
     *
     * @return string
     */
    public static function printAlertHistoryBackAndExit($exception, $msg = null)
    {
        if ($exception instanceof MsgException) {
            return UrlHelper::printAlertHistoryBack($exception->getMessage());
        }

        SentryHelper::triggerSentryException($exception);

        if (StringUtils::isEmpty($msg)) {
            $msg = '오류가 발생하였습니다. 다시 시도하여 주세요. 문제가 다시 발생할 경우 해당 부서에 문의하여주세요.';
        }

        return UrlHelper::printAlertHistoryBack($msg);
    }
}
