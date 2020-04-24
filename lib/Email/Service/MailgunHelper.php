<?php

namespace Ridibooks\Platform\Common\Email\Service;

use Mailgun\Exception\HttpClientException;
use Mailgun\Mailgun;
use Mailgun\Model\EmailValidation\ValidateResponse;
use Mailgun\Model\Message\SendResponse;
use Ridibooks\Platform\Common\Email\Constant\EmailContentTypeConst;

class MailgunHelper
{
    /** @var Mailgun */
    private $mailgun;
    /** @var string */
    private $send_domain;
    /** @var bool */
    private $is_testmode;

    /** @var self */
    private static $instance;

    public static function getInstance(
        ?string $api_key = null,
        ?string $send_domain = null,
        bool $is_testmode = false
    ): self {
        if (self::$instance === null) {
            if (empty($api_key) || empty($send_domain)) {
                throw new \InvalidArgumentException('Not set api_key. send_domain. require them');
            }
            self::$instance = new self($api_key, $send_domain, $is_testmode);
        }

        return self::$instance;
    }

    private function __construct(string $api_key, string $send_domain, bool $is_testmode)
    {
        $this->mailgun = Mailgun::create($api_key);
        $this->send_domain = $send_domain;
        $this->is_testmode = $is_testmode;
    }

    public function setTestMode(bool $is_testmode): self
    {
        $this->is_testmode = $is_testmode;

        return $this;
    }

    /**
     * @param string   $from
     * @param string[] $to
     * @param string   $subject
     * @param string   $content
     * @param string[] $cc
     * @param string[] $bcc
     * @param string   $content_type
     * @param array    $attachments [filename => filepath]
     *
     * @return bool
     */
    public function send(
        string $from,
        array $to,
        string $subject,
        string $content,
        string $content_type = EmailContentTypeConst::TEXT_HTML,
        array $cc = [],
        array $bcc = [],
        array $attachments = []
    ): bool {
        $parameters = $this->prepareParameters($from, $to, $subject, $content, $content_type, $cc, $bcc);

        $formatted_attachments = [];
        foreach ($attachments as $name => $path) {
            $formatted_attachments[] = $this->generateMailgunAttachmentFormat($name, $path);
        }

        if (!empty($formatted_attachments)) {
            $parameters['attachment'] = $formatted_attachments;
        }

        try {
            /** @var SendResponse $result */
            $result = $this->mailgun->messages()->send($this->send_domain, $parameters);

            return !empty($result->getId());
        } catch (HttpClientException $e) {
            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string   $from
     * @param string[] $to
     * @param string   $subject
     * @param string   $content
     * @param string[] $cc
     * @param string[] $bcc
     * @param string   $content_type
     *
     * @return array
     */
    private function prepareParameters(
        string $from,
        array $to,
        string $subject,
        string $content,
        string $content_type = EmailContentTypeConst::TEXT_HTML,
        array $cc = [],
        array $bcc = []
    ): array {
        $parameters = [
            'from' => $from,
            'to' => implode(',', $to),
            'subject' => $subject,
        ];

        if ($content_type === EmailContentTypeConst::TEXT_HTML) {
            $parameters['html'] = $content;
        } else {
            $parameters['text'] = $content;
        }

        if (!empty($cc)) {
            $parameters['cc'] = $cc;
        }

        if (!empty($bcc)) {
            $parameters['bcc'] = $bcc;
        }

        if ($this->is_testmode) {
            $parameters['o:testmode'] = 'true';
        }

        return $parameters;
    }

    public function isVaildEmail(string $email): bool
    {
        try {
            /** @var ValidateResponse $result */
            $result = $this->mailgun->emailValidation()->validate($email);

            return $result->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateMailgunAttachmentFormat(string $name, string $attachment): array
    {
        return [
            'filePath' => $attachment,
            'filename' => $name,
        ];
    }
}
