<?php

namespace Ridibooks\Platform\Common\Email\Service;

use Mailgun\Exception\HttpClientException;
use Mailgun\Mailgun;
use Ridibooks\Platform\Common\Email\Constant\EmailContentTypeConst;
use Ridibooks\Platform\Common\Security\Encryption;

/**
 * 환경변수 의존성 존재함
 * \Config::$MAILGUN_API_KEY
 * \Config::$MAILGUN_SEND_DOMAIN
 * \Config::$MAILGUN_PUBLIC_ID
 * \Config::$MAILGUN_PUBLIC_KEY
 * \Config::$MAILGUN_EMAIL_VALIDATE_API_URL
 * \Config::$MAILGUN_SECRET_KEY_OPT_OUT
 */
class MailgunHelper
{
    private const RESPONSE_HTTP_OK = 200;
    /** @var Mailgun */
    private $mailgun;

    public function __construct(string $api_key)
    {
        $this->mailgun = new Mailgun($api_key);
    }

    public function getBounces(int $skip = 0, int $limit = 1)
    {
        $result = $this->mailgun->get(\Config::$MAILGUN_SEND_DOMAIN . "/bounces", ['skip' => $skip, 'limit' => $limit]);

        return $result->http_response_body;
    }

    /**
     * @param string        $from
     * @param string[]      $to
     * @param string        $subject
     * @param string        $content
     * @param string[] $cc
     * @param string[] $bcc
     * @param string        $content_type
     * @param array         $attachments
     * @param bool          $is_testmode
     *
     * @return \stdClass
     * @throws \Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters
     */
    public function send(
        string $from,
        array $to,
        string $subject,
        string $content,
        array $cc = [],
        array $bcc = [],
        string $content_type = EmailContentTypeConst::TEXT_HTML,
        array $attachments = [],
        bool $is_testmode = false
    ) {
        $message = self::prepareCommonParameters(
            $from,
            $to,
            $subject,
            $content,
            $content_type,
            $cc,
            $bcc,
            $is_testmode
        );
        $message['content-type'] = $content_type;

        $formatted_attachments = [];
        foreach ($attachments as $name => $path) {
            $formatted_attachments[] = self::generateMailgunAttachmentFormat($name, $path);
        }

        $files = [];
        if (!empty($formatted_attachments)) {
            $files['attachment'] = $formatted_attachments;
        }

        return $this->mailgun->sendMessage(\Config::$MAILGUN_SEND_DOMAIN, $message, $files);
    }

    /**
     * @param string        $from
     * @param string[]      $to
     * @param string        $subject
     * @param string        $content
     * @param string        $content_type
     * @param string[] $cc
     * @param string[] $bcc
     * @param bool          $is_testmode
     *
     * @return bool
     */
    public static function sendUsingV3(
        string $from,
        array $to,
        string $subject,
        string $content,
        string $content_type = EmailContentTypeConst::TEXT_HTML,
        array $cc = [],
        array $bcc = [],
        bool $is_testmode = false
    ) {
        $mg = Mailgun::create(\Config::$MAILGUN_API_KEY);

        $parameters = self::prepareCommonParameters(
            $from,
            $to,
            $subject,
            $content,
            $content_type,
            $cc,
            $bcc,
            $is_testmode
        );

        try {
            $mg->messages()->send(\Config::$MAILGUN_SEND_DOMAIN, $parameters);

            return true;
        } catch (HttpClientException $e) {
            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string        $from
     * @param string[]      $to
     * @param string        $subject
     * @param string        $content
     * @param string[] $cc
     * @param string[] $bcc
     * @param string        $content_type
     * @param bool          $is_testmode
     *
     * @return array
     */
    private static function prepareCommonParameters(
        string $from,
        array $to,
        string $subject,
        string $content,
        string $content_type = EmailContentTypeConst::TEXT_HTML,
        array $cc = [],
        array $bcc = [],
        bool $is_testmode = false
    ): array {
        $parameters = [
            'from' => $from,
            'to' => implode(',', $to),
            'subject' => $subject,
            'recipient-variables' => self::createRecipientVariables($to),
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

        if ($is_testmode) {
            $parameters['o:testmode'] = 'true';
        }

        return $parameters;
    }

    /**
     * @param \stdClass $results
     *
     * @return bool
     */
    public static function isSuccess($results): bool
    {
        if ($results->http_response_code != self::RESPONSE_HTTP_OK) {
            trigger_error('[EMAIL][Mailgun] Error : ' . $results->http_response_code);

            return false;
        }

        return true;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public static function isVaildEmail($email)
    {
        $url = \Config::$MAILGUN_EMAIL_VALIDATE_API_URL;
        $querystring = 'address=' . urlencode($email);

        $user_id = \Config::$MAILGUN_PUBLIC_ID;
        $user_password = \Config::$MAILGUN_PUBLIC_KEY;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$querystring}");
        curl_setopt($ch, CURLOPT_USERPWD, "{$user_id}:{$user_password}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $json_result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json_result, true);

        return $result['is_valid'];
    }

    /**
     * @param string[] $recipients
     *
     * @return string|false
     */
    private static function createRecipientVariables(array $recipients)
    {
        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }
        $recipient_vars = [];
        foreach ($recipients as $recipient) {
            $recipient_vars[$recipient] = [
                'optout_link' => self::getEmailOptoutLinkByEmail($recipient),
            ];
        }

        return json_encode($recipient_vars);
    }

    private static function getEmailOptoutLinkByEmail(string $email): string
    {
        $domain = \Config::$MAILGUN_SEND_DOMAIN;
        $token = self::encryptedEmail($email);

        return "https://$domain/account/marketing-agreement/email?token=$token";
    }

    /**
     * @param string $email
     *
     * @return string|bool
     */
    private static function encryptedEmail(string $email)
    {
        return Encryption::encryptAESBase64($email, \Config::$MAILGUN_SECRET_KEY_OPT_OUT);
    }

    private static function generateMailgunAttachmentFormat(string $name, string $attachment): array
    {
        $format = [
            'filePath' => $attachment,
            'remoteName' => $name,
        ];

        return $format;
    }
}
