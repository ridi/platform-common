<?php

namespace Ridibooks\Platform\Common\Email\Service;

use Mailgun\Exception\HttpClientException;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\Hydrator;
use Mailgun\Mailgun;
use Mailgun\Model\EmailValidation\ValidateResponse;
use Mailgun\Model\Message\SendResponse;
use Ridibooks\Platform\Common\Email\Constant\EmailContentTypeConst;

class MailgunHelper
{
    /** @var HttpClientConfigurator */
    private $configurator;
    /** @var Hydrator */
    private $hydrator;

    /** @var string */
    private $send_domain;
    /** @var bool */
    private $is_testmode;

    /** @var self */
    private static $instance;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->configurator = new HttpClientConfigurator();
    }

    public function setApiKey(string $api_key): self
    {
        $this->configurator->setApiKey($api_key);

        return $this;
    }

    public function setSendDomain(string $send_domain): self
    {
        $this->send_domain = $send_domain;

        return $this;
    }

    public function setTestMode(bool $is_testmode, ?string $test_url = ''): self
    {
        if ($is_testmode && empty($test_url)) {
            throw new \InvalidArgumentException('Invalid test_url');
        }

        $this->is_testmode = $is_testmode;
        $this->configurator->setDebug($this->is_testmode);
        if ($is_testmode) {
            $this->configurator->setEndpoint($test_url);
        }

        return $this;
    }

    public function setHydrator(Hydrator $hydrator): self
    {
        $this->hydrator = $hydrator;

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
        $this->assert();

        $parameters = $this->prepareParameters($from, $to, $subject, $content, $content_type, $cc, $bcc);

        $formatted_attachments = [];
        foreach ($attachments as $name => $path) {
            $formatted_attachments[] = $this->generateMailgunAttachmentFormat($name, $path);
        }

        if (!empty($formatted_attachments)) {
            $parameters['attachment'] = $formatted_attachments;
        }

        try {
            $mailgun = new Mailgun($this->configurator, $this->hydrator);
            /** @var SendResponse $result */
            $result = $mailgun->messages()->send($this->send_domain, $parameters);

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
            $parameters['o:testmode'] = 'yes';
        }

        return $parameters;
    }

    public function isVaildEmail(string $email): bool
    {
        $this->assert();

        try {
            $mailgun = new Mailgun($this->configurator, $this->hydrator);
            /** @var ValidateResponse $result */
            $result = $mailgun->emailValidation()->validate($email);

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

    private function assert(): void
    {
        if (empty($this->send_domain)) {
            throw new \InvalidArgumentException('Not set send_domain. require them');
        }
    }
}
