<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Email\Dto;

use Ridibooks\Platform\Common\Util\DictsUtils;

class HtmlTableDto
{
    /** @var string */
    public $title;
    /** @var array */
    public $dicts;

    public static function importFromDicts(array $dicts): self
    {
        $dto = new self();
        $dto->title = '';
        $dto->dicts = $dicts;

        return $dto;
    }

    public static function importFromTitleAndDicts(string $title, $dicts = []): self
    {
        $dto = new self();
        $dto->title = $title;
        $dto->dicts = $dicts;

        return $dto;
    }

    public function exportToHtml(): string
    {
        if (!empty($this->dicts)) {
            $html_table = DictsUtils::convertDictsToHtmlTable($this->dicts);
        } else {
            $html_table = DictsUtils::convertDictsToHtmlTable([['' => '[해당 내역은 없습니다]']]);
        }

        return $this->title . $html_table;
    }
}
