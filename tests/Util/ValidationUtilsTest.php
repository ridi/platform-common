<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\ValidationUtils;

class ValidationUtilsTest extends TestCase
{
    private const MSG = '올바르게 입력요망';

    /**
     * @param $phone_number
     *
     * @dataProvider providerValidatePhoneForSuccess
     */
    public function testValidatePhoneForSuccess($phone_number)
    {
        ValidationUtils::checkPhoneNumber($phone_number, self::MSG);
        $this->assertTrue(true);
    }

    public function providerValidatePhoneForSuccess()
    {
        return [
            ['010-1234-5678'],
            ['01012345678'],
            ['02-123-4567'],
            ['021234567']
        ];
    }

    /**
     * @param $phone_number
     *
     * @dataProvider providerValidatePhoneForFail
     */
    public function testValidatePhoneForFail($phone_number)
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkPhoneNumber($phone_number, self::MSG);
    }

    public function providerValidatePhoneForFail()
    {
        return [
            ['0101-1234-5678'],
            ['010-12341-5678'],
            ['010-1234-56781'],
            ['010-123-56781'],
            ['010123456789'],
            ['310-1234-5678'],
            ['310-1234-a567']
        ];
    }

    /**
     * @param $mail
     *
     * @dataProvider providerValidateMailForSuccess
     */
    public function testValidateMailForSuccess($mail)
    {
        ValidationUtils::checkMailAddress($mail, self::MSG);
        $this->assertTrue(true);
    }

    public function providerValidateMailForSuccess()
    {
        return [
            ['ridi.ridi@ridi.com.kr'],
            ['ridi_123@ridi.com'],
            ['ridi+-_.ridi@ridi.com'],
            ['ridi.ridi@ridi.com.kr.org'],
            ['xxxx@aa-aaa.co.kr']
        ];
    }

    /**
     * @param $mail
     *
     * @dataProvider providerValidateMailForFail
     */
    public function testValidateMailForFail($mail)
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkMailAddress($mail, self::MSG);
    }

    public function providerValidateMailForFail()
    {
        return [
            ['ridi_ridi!#@ridi.com'],
            ['ridi@ridi+.com'],
            ['ridi@ridi_.com'],
        ];
    }
}
