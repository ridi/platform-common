<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Email\Service;

class EmailServiceFromHtml extends EmailService
{
    public function setBody(?string $body, bool $is_nl2br = false): void
    {
        $this->setBodyHeader('');
        $this->setBodyArticle($body);
        $this->setBodyFooter('');
    }

    public function getBodyArticle(): string
    {
        return $this->body_article;
    }
}
