<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\CountryUtils;

class CountryUtilsTest extends TestCase
{
    public function testGetCountryNameByCode(): void
    {
        $this->assertEquals(null, CountryUtils::getCountryNameByCode('NOT_EXISTS'));
        $this->assertEquals('대한민국', CountryUtils::getCountryNameByCode('KR'));
    }
}
