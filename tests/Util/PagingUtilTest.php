<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\PagingUtil;

class PagingUtilTest extends TestCase
{
    /**
     * @dataProvider providerShortenSequential
     */
    public function testPaging($total, $cur_page, $exptected): void
    {
        $paging = new PagingUtil($total, $cur_page);

        $this->assertEquals($exptected[0], $paging->num_page_group);
        $this->assertEquals($exptected[1], $paging->last_page_group);
        $this->assertEquals($exptected[2], $paging->start_page);
        $this->assertEquals($exptected[3], $paging->end_page);
        $this->assertEquals($exptected[4], $paging->next_page_group);
        $this->assertEquals($exptected[5], $paging->prev_page_group);
        $this->assertEquals($exptected[6], $paging->total_page);
        $this->assertEquals($exptected[7], $paging->cpage);
        $this->assertEquals($exptected[8], $paging->start);
    }

    public function providerShortenSequential(): array
    {
        return [
            'case 1. 첫 페이지' => [
                1000,
                1,
                [1, 10, 1, 10, 11, 1, 100, 1, 0],
            ],
            'case 2. 두번째 페이지' => [
                1000,
                11,
                [2, 10, 11, 20, 21, 10, 100, 11, 100],
            ],
            'case 3. 끝 페이지' => [
                1000,
                100,
                [10, 10, 91, 100, 100, 90, 100, 100, 990],
            ],
            'case 4. 존재하지 않는 페이지 1 - 이전' => [
                1000,
                0,
                [1, 10, 1, 10, 11, 1, 100, 1, 0],
            ],
            'case 5. 존재하지 않는 페이지 2 - 이후' => [
                1000,
                101,
                [10, 10, 91, 100, 100, 90, 100, 100, 990],
            ],
        ];
    }
}
