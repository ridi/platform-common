<?php

namespace Ridibooks\Platform\Common\Email\Service;

use Ridibooks\Platform\Common\Cache\AdaptableCache;
use Ridibooks\Platform\Common\Email\Constant\EmailContentTypeConst;
use Ridibooks\Platform\Common\Util\SentryHelper;
use Ridibooks\Platform\Common\Validation\ValidationHelper;

class EmailBaseSender implements \JsonSerializable
{
    private const ERROR_LOGGING_THRESHOLD = 10;
    private const ERROR_LOGGING_TTL = 60;
    private const ERROR_LOGGING_KEY = 'error_logging_email_send_fail';

    /** @var string|null */
    private $from;
    /** @var string[] */
    private $to = [];
    /** @var string[] */
    private $cc = [];
    /** @var string[] */
    private $bcc = [];
    /** @var string|null */
    private $subject;
    /** @var string|null */
    private $body;
    /** @var array */
    private $attachments = [];
    /** @var string */
    private $content_type = EmailContentTypeConst::TEXT_HTML;

    /**
     * 이메일 전송에 필수적인 파라미터들이 모두 설정되었는지 확인
     * @return bool
     */
    private function hasEnoughParameters(): bool
    {
        return !(empty($this->from) || empty($this->to) || empty($this->subject) || empty($this->body));
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }

    /**
     * @param string[] $emails
     *
     * @return string[]
     */
    private static function getValidEmails(array $emails): array
    {
        $email_arr = [];
        foreach ($emails as $email) {
            if (self::isValidEmail($email)) {
                $email_arr[] = trim($email);
            }
        }

        return $email_arr;
    }

    private static function isValidEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        $email = trim($email);

        if (preg_match("/<(.+@.+)>$/", $email, $emailMatch)) {
            $email = $emailMatch[1];
        }

        return ValidationHelper::isValidEmailAddress($email);
    }

    public function setFrom(string $email, ?string $name = null): bool
    {
        if (!self::isValidEmail($email)) {
            return false;
        }

        // format : sender name <sender@email.com>
        $this->from = (trim($name) === '') ? trim($email) : trim($name) . ' <' . trim($email) . '>';

        return true;
    }

    /**
     * @param string[] $to
     */
    public function setTo(array $to): void
    {
        $this->to = self::getValidEmails($to);
    }

    /**
     * @param string[] $cc
     */
    public function setCc(array $cc): void
    {
        $this->cc = self::getValidEmails($cc);
    }

    /**
     * @param string[] $bcc
     */
    public function setBcc(array $bcc): void
    {
        $this->bcc = self::getValidEmails($bcc);
    }

    public function setSubject(string $subject): bool
    {
        if (empty($subject)) {
            return false;
        }

        $this->subject = $subject;

        return true;
    }

    public function setBody(?string $body, bool $is_nl2br = false): void
    {
        if (empty($body)) {
            return;
        }

        $this->body = ($is_nl2br) ? (nl2br($body)) : ($body);
    }

    /**
     * @param array $attachments [전달할 파일명 => 파일경로]에 대한 배열
     */
    public function setAttachments(array $attachments): void
    {
        $filtered_attachments = [];
        foreach ($attachments as $name => $path) {
            if (is_file($path)) {
                $filtered_attachments[$name] = $path;
            }
        }

        $this->attachments = $filtered_attachments;
    }

    public function setContentType(string $content_type): void
    {
        $this->content_type = $content_type;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @return string[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return string[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return string[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param bool $is_testmode 테스트 모드일 경우 실제로 전송되지 않음
     *
     * @return bool
     */
    public function sendMail(bool $is_testmode = false): bool
    {
        return $this->sendWithMailgun($is_testmode);
    }

    private function sendWithMailgun(bool $is_testmode = false): bool
    {
        if (!$this->hasEnoughParameters()) {
            return false;
        }

        try {
            $helper = new MailgunHelper(\Config::$MAILGUN_API_KEY);
            $results = $helper->send(
                $this->from,
                $this->to,
                $this->subject,
                $this->body,
                $this->cc,
                $this->bcc,
                $this->content_type,
                $this->attachments,
                $is_testmode
            );
        } catch (\Exception $e) {
            // 대부분 일시적인 통신에러이기 때문에 지속발생시 로깅한다.
            if (AdaptableCache::isOverThresholdWithInc(
                self::ERROR_LOGGING_KEY,
                self::ERROR_LOGGING_THRESHOLD,
                self::ERROR_LOGGING_TTL
            )
            ) {
                SentryHelper::triggerSentryException($e);
            }

            return false;
        }

        return MailgunHelper::isSuccess($results);
    }
}
