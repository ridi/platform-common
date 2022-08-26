<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Email\Service;

class EmailServiceFromDicts extends EmailService
{
    /** @var array */
    public $body_article_dicts = [];

    public function setBodyArticleFromDicts(array $dicts): void
    {
        $this->body_article_dicts = $dicts;
    }

    public function getBodyArticle(): string
    {
        return self::convertDictsToHtmlTable($this->body_article_dicts);
    }
}
