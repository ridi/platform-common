<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Email\Service;

use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\DictsUtils;

abstract class EmailService extends EmailBaseSender
{
    /** @var string */
    protected $body_header = '';
    /** @var string */
    protected $body_article = '';
    /** @var string */
    protected $body_footer = '';

    public function setBodyHeader(string $body_header = ''): void
    {
        $this->body_header = $body_header;
    }

    public function setBodyArticle(string $body_article): void
    {
        $this->body_article = $body_article;
    }

    abstract public function getBodyArticle(): string;

    public function setBodyFooter(string $body_footer = ''): void
    {
        $this->body_footer = $body_footer;
    }

    public function initBody(): void
    {
        $this->setBodyArticle($this->getBodyArticle());
        $body = implode('<br><br>', [$this->body_header, $this->body_article, $this->body_footer]);

        parent::setBody($body);
    }

    /**
     * @param string[] $to
     */
    public function setTo(array $to): void
    {
        $to = self::filterMails($to);

        parent::setTo($to);
    }

    /**
     * @param string[] $cc
     */
    public function setCc(array $cc): void
    {
        $cc = self::filterMails($cc);

        parent::setCc($cc);
    }

    /**
     * @param string[] $bcc
     */
    public function setBcc(array $bcc): void
    {
        $bcc = self::filterMails($bcc);

        parent::setBcc($bcc);
    }

    public function sendMail(bool $is_testmode = false): bool
    {
        $this->initBody();

        if (\Config::$UNDER_DEV) {
            $this->devEmailWrapper();
        }

        return parent::sendMail();
    }

    public function devEmailWrapper(): void
    {
        if (empty(\Config::$DEV_MAIL_RECEIVER)) {
            throw new MsgException('Config::$DEV_MAIL_RECEIVER 에 값이 없습니다');
        }
        $cc = $this->getCc();
        if (!empty($cc)) {
            $this->setCc([\Config::$DEV_MAIL_RECEIVER]);
        }
        $bcc = $this->getBcc();
        if (!empty($bcc)) {
            $this->setBcc([\Config::$DEV_MAIL_RECEIVER]);
        }
        $this->setTo([\Config::$DEV_MAIL_RECEIVER]);
        $this->setSubject('[개발테스트]' . $this->getSubject());
    }

    /**
     * @param string[] $mail_receiver
     *
     * @return string[]
     */
    private static function filterMails(array $mail_receiver): array
    {
        return array_filter(array_unique($mail_receiver));
    }

    /**
     * @param array|null $dicts
     * @return string
     */
    public static function convertDictsToHtmlTable($dicts = []): string
    {
        if (empty($dicts)) {
            return DictsUtils::convertDictsToHtmlTable([['' => '[해당 내역은 없습니다]']]);
        }

        $dicts = collect($dicts)->map(
            function (array $dict) {
                if (empty($dict)) {
                    return ['' => '[해당 내역은 없습니다]'];
                }

                return $dict;
            }
        )->all();

        return DictsUtils::convertDictsToHtmlTable($dicts);
    }
}
