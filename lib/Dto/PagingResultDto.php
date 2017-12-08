<?php
namespace Ridibooks\Platform\Common\Dto;

class PagingResultDto
{
    /**
     * @var array
     */
    public $items;

    /**
     * @var int
     */
    public $total_count;

    public static function importDatabase($items, $total_count)
    {
        $dto = new self;
        $dto->items = $items;
        $dto->total_count = $total_count;

        return $dto;
    }
}
