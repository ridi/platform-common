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

    /** @var bool */
    private $is_devmode;
    /** @var string */
    private $dev_mail_receiver;

    public function __construct(bool $is_devmode = false, string $dev_mail_receiver = '')
    {
        $this->is_devmode = $is_devmode;
        $this->dev_mail_receiver = $dev_mail_receiver;
    }

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

    public function sendMail(): bool
    {
        $this->initBody();

        if ($this->is_devmode) {
            $this->devEmailWrapper();
        }

        return parent::sendMail();
    }

    public function devEmailWrapper(): void
    {
        if (empty($this->dev_mail_receiver)) {
            throw new MsgException('dev_mail_receiver 에 값이 없습니다');
        }

        $cc = $this->getCc();
        if (!empty($cc)) {
            $this->setCc([$this->dev_mail_receiver]);
        }
        $bcc = $this->getBcc();
        if (!empty($bcc)) {
            $this->setBcc([$this->dev_mail_receiver]);
        }
        $this->setTo([$this->dev_mail_receiver]);
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
