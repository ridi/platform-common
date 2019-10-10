<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Email\Service;

use Ridibooks\Platform\Common\Email\Dto\HtmlTableDto;

class EmailServiceFromHtmlTableDto extends EmailService
{
    /** @var HtmlTableDto[] */
    private $body_article_html_table_dtos = [];

    /**
     * 타이틀이 있는 테이블을 만듭니다.
     * @param HtmlTableDto $html_table_dto
     */
    public function setBodyArticleFromHtmlTableDto(HtmltableDto $html_table_dto): void
    {
        $this->body_article_html_table_dtos = [$html_table_dto];
    }

    /**
     * 타이틀이 있는 테이블들을 만듭니다.
     * @param HtmlTableDto[] $html_table_dtos
     */
    public function setBodyArticleFromHtmlTableDtos(array $html_table_dtos): void
    {
        $this->body_article_html_table_dtos = $html_table_dtos;
    }

    public function getBodyArticle(): string
    {
        return collect($this->body_article_html_table_dtos)->map(
            function (HtmlTableDto $dto) {
                return $dto->title . '<br>' . self::convertDictsToHtmlTable($dto->dicts);
            }
        )->implode('<br><br>');
    }
}
