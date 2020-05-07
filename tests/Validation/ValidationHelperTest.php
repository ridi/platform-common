<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Validation\ValidationHelper;

class ValidationHelperTest extends TestCase
{
    /** @dataProvider providerEmail */
    public function testEmail(string $email, bool $expected): void
    {
        $this->assertEquals(ValidationHelper::isValidEmailAddress($email), $expected);
    }

    public function providerEmail(): array
    {
        return [
            ['ridi.ridi@ridi.com.kr', true],
            ['ridi_123@ridi.com', true],
            ['ridi+-_.ridi@ridi.com', true],
            ['ridi.ridi@ridi.com.kr.org', true],
            ['xxxx@aa-aaa.co.kr', true],
            ['test.@gmail.com', true],
            ['ridi_ridi!#@ridi.com', true],
            ['ridi@ridi+.com', false],
            ['ridi@ridi_.com', false],
        ];
    }

    /** @dataProvider providerRfcEmail */
    public function testRfcEmail(string $email, bool $expected): void
    {
        $this->assertEquals(ValidationHelper::isValidEmailByRfc($email), $expected);
    }

    public function providerRfcEmail(): array
    {
        return [
            ['ridi.ridi@ridi.com.kr', true],
            ['ridi_123@ridi.com', true],
            ['ridi+-_.ridi@ridi.com', true],
            ['ridi.ridi@ridi.com.kr.org', true],
            ['xxxx@aa-aaa.co.kr', true],
            ['test.@gmail.com', false],
            ['ridi_ridi!#@ridi.com', true],
            ['ridi@ridi+.com', true],
            ['ridi@ridi_.com', true],
        ];
    }
}
