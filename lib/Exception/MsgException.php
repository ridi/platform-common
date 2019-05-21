<?php
namespace Ridibooks\Platform\Common\Exception;

use Ridibooks\Library\SentryHelper;

class MsgException extends \Exception
{
    private $should_be_logged = false;

    /**
     * @param string $message
     * @param bool $should_be_logged
     */
    public function __construct($message = "", $should_be_logged = false)
    {
        parent::__construct($message);

        $this->should_be_logged = $should_be_logged;

        // $should_be_logged가 설정되면 Exception 생성 즉시 Sentry Logging
        if ($should_be_logged) {
            if (!SentryHelper::triggerSentryException($this)) {
                trigger_error($this);
            }
        }
    }

    /**
     * @return bool
     */
    public function shouldBeLogged()
    {
        return $this->should_be_logged;
    }
}
