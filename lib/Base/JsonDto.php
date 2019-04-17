<?php

namespace Ridibooks\Platform\Common\Base;

use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Library\SentryHelper;
use Ridibooks\Platform\Common\Util\StringUtils;

class JsonDto
{
    public $success;
    public $msg;
    public $data;

    public function __construct()
    {
        $this->success = true;
        $this->msg = [];
    }

    /**메시지 set
     * @param $msg_text
     */
    public function setMsg($msg_text)
    {
        array_push($this->msg, $msg_text);
    }

    /**Exception 발생 시 set<br/>
     * MsgException일 경우 exception msg만 보여준다.
     * 일반 Exception일 경우 exception을 외부에 노출시키지 않고 trigger_error을 통하여 sentry에 exception 알린다.
     * @param \Exception $exception
     * @param string $msg 기본적으로 보여줄 문구
     */
    public function setException($exception, $msg = null)
    {
        if ($exception instanceof MsgException) {
            $this->setMsgException($exception);
        } else {
            SentryHelper::triggerSentryException($exception);

            if (StringUtils::isEmpty($msg)) {
                $msg = "오류가 발생하였습니다. 다시 시도하여 주세요. 문제가 다시 발생할 경우 개발그룹에 문의하여주세요.";
            }
            $this->success = false;
            $this->setMsg($msg);
        }
    }

    /**MsgException 발생 시 exception 정보 set 한다.
     * @param \Ridibooks\Exception\MsgException $msgException
     */
    public function setMsgException($msgException)
    {
        $this->success = false;
        $this->setMsg($msgException->getMessage());
    }
}
