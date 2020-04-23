<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\DateUtils;

class DateUtilsTest extends TestCase
{
    public function testCreateNowExceptMicroseconds(): void
    {
        $date_time = DateUtils::createNowExceptMicroseconds();
        $datetime = $date_time->format('Y-m-d H:i:s.u');
        $split = explode('.', $datetime);
        $count = count($split);
        $this->assertEquals(2, $count);
        $this->assertEquals('000000', $split[1]);
    }
}
