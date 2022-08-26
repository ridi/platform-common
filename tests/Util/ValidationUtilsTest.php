<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\ValidationUtils;

class ValidationUtilsTest extends TestCase
{
    private const MSG = '올바르게 입력요망';

    public function testMatch(): void
    {
        $this->assertTrue(ValidationUtils::match('/\d{4}-\d{2}-\d{2}/', '2020-01-01'));
        $this->assertFalse(ValidationUtils::match('/\d{4}-\d{2}-\d{2}/', '20200101'));
    }

    public function testCheckNullFieldForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkNullField('', self::MSG);
    }

    public function testCheckNullFieldForSuccess(): void
    {
        ValidationUtils::checkNullField('test', self::MSG);
        $this->assertTrue(true);
    }


    /**
     * @dataProvider providerCheckNumberFieldForSuccess
     */
    public function testCheckNumberFieldForSuccess(string $number): void
    {
        ValidationUtils::checkNumberField($number, self::MSG);
        $this->assertTrue(true);
    }

    public function providerCheckNumberFieldForSuccess(): array
    {
        return [
            'case 1. 일반' => ['1'],
            'case 2. 일반' => ['111'],
            'case 3. 일반' => ['111111'],
            'case 4. 일반' => ['11111111111'],
            'case 5. 소수점' => ['111.1111'],
        ];
    }

    /**
     * @dataProvider providerCheckNumberFieldForFail
     */
    public function testCheckNumberFieldForFail(string $number): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkNumberField($number, self::MSG);
    }

    public function providerCheckNumberFieldForFail(): array
    {
        return [
            'case 1. 콤마' => ['11,111'],
            'case 2. 콤마' => ['1,111,111'],
        ];
    }


    /**
     * @dataProvider providerCheckMinLengthForSuccess
     */
    public function testCheckMinLengthForSuccess($field, int $min_length): void
    {
        ValidationUtils::checkMinLength($field, $min_length, self::MSG);
        $this->assertTrue(true);
    }

    public function providerCheckMinLengthForSuccess(): array
    {
        return [
            'case 1-1. int형' => [111, 1],
            'case 1-2. string형' => ['111', 1],
            'case 2-1. int형' => [111222333444, 10],
            'case 2-2. string형' => ['111222333444', 10],
            'case 3. 한글' => ['한글', 1],
            'case 4. 한글' => ['한글테스트', 4],
        ];
    }

    /**
     * @dataProvider providerCheckMinLengthForFail
     */
    public function testCheckMinLengthForFail($field, int $min_length): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkMinLength($field, $min_length, self::MSG);
    }

    public function providerCheckMinLengthForFail(): array
    {
        return [
            'case 1-1. int형' => [111, 5],
            'case 1-2. string형' => ['111', 5],
            'case 2-1. int형' => [111222333444, 20],
            'case 2-2. string형' => ['111222333444', 20],
            'case 3. 한글' => ['한글', 5],
            'case 4. 한글' => ['한글테스트', 10],
        ];
    }


    /**
     * @dataProvider providerCheckLengthForSuccess
     */
    public function testCheckLengthForSuccess($field, int $min_length): void
    {
        ValidationUtils::checkLength($field, $min_length, self::MSG);
        $this->assertTrue(true);
    }

    public function providerCheckLengthForSuccess(): array
    {
        return [
            'case 1-1. int형' => [111, 3],
            'case 1-2. string형' => ['111', 3],
            'case 2-1. int형' => [111222333444, 12],
            'case 2-2. string형' => ['111222333444', 12],
            'case 3. 한글' => ['한글', 2],
            'case 4. 한글' => ['한글테스트', 5],
        ];
    }

    /**
     * @dataProvider providerCheckLengthForFail
     */
    public function testCheckLengthForFail($field, int $min_length): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkLength($field, $min_length, self::MSG);
    }

    public function providerCheckLengthForFail(): array
    {
        return [
            'case 1-1. int형' => [111, 5],
            'case 1-2. string형' => ['111', 5],
            'case 2-1. int형' => [111222333444, 20],
            'case 2-2. string형' => ['111222333444', 20],
            'case 3. 한글' => ['한글', 5],
            'case 4. 한글' => ['한글테스트', 10],
        ];
    }


    /**
     * @dataProvider providerCheckDatetimeFormatForSuccess
     */
    public function testCheckDatetimeFormatForSuccess(string $field, string $format): void
    {
        ValidationUtils::checkDatetimeFormat($field, $format, self::MSG);
        $this->assertTrue(true);
    }

    public function providerCheckDatetimeFormatForSuccess(): array
    {
        return [
            'case 1. 년월일시분초' => ['2020-01-01 11:11:11', 'Y-m-d H:i:s'],
            'case 2. 년월일시분' => ['2020-01-01 11:11', 'Y-m-d H:i'],
            'case 3. 년월일' => ['2020-01-01', 'Y-m-d'],
        ];
    }

    /**
     * @dataProvider providerCheckDatetimeFormatForFail
     */
    public function testCheckDatetimeFormatForFail(string $field, string $format): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkDatetimeFormat($field, $format, self::MSG);
    }

    public function providerCheckDatetimeFormatForFail(): array
    {
        return [
            'case 1. 년월일시분초 vs 년월일시분' => ['2020-01-01 11:11:11', 'Y-m-d H:i'],
            'case 2. 년월일시분 vs 년월일' => ['2020-01-01 11:11', 'Y-m-d'],
            'case 3. 년월일 vs 년월일시' => ['2020-01-01', 'Y-m-d H:i'],
        ];
    }



    public function testCheckDatetimePeriodForSuccess(): void
    {
        ValidationUtils::checkDatetimePeriod('2020-01-01 11:11:11', '2020-01-01 12:12:12', self::MSG);
        $this->assertTrue(true);
    }

    public function testCheckDatetimePeriodForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkDatetimePeriod('2020-01-01 12:12:12', '2020-01-01 11:11:11', self::MSG);
    }

    public function testCheckIsbn10NumberForSuccess(): void
    {
        ValidationUtils::checkIsbn10Number('9992158107');
        $this->assertTrue(true);
    }

    public function testCheckIsbn10NumberForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkIsbn10Number('0000000001');
    }

    public function testCheckIsbn13NumberForSuccess(): void
    {
        ValidationUtils::checkIsbn13Number('9780306406157');
        $this->assertTrue(true);
    }

    public function testCheckIsbn13NumberForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkIsbn13Number('0000000000001');
    }

    public function testCheckEcnForSuccess(): void
    {
        ValidationUtils::checkEcn('ECN-0102-2008-000-123456789');
        $this->assertTrue(true);
    }

    public function testCheckEcnForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkEcn('0102-2008-000-123456788');
    }

    public function testCheckIssnForSuccess(): void
    {
        ValidationUtils::checkIssn('0378-5955');
        $this->assertTrue(true);
    }

    public function testCheckIssnForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkIssn('0378-5954');
    }

    public function testCheckHtmlForSuccess(): void
    {
        ValidationUtils::checkHtml('<b></b>', self::MSG);
        $this->assertTrue(true);
    }

    public function testCheckHtmlForFail(): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkHtml('<b><u></b></u>', self::MSG);
    }


    /**
     * @dataProvider providerValidatePhoneForSuccess
     */
    public function testValidatePhoneForSuccess(string $phone_number): void
    {
        ValidationUtils::checkPhoneNumber($phone_number, self::MSG);
        $this->assertTrue(true);
    }

    public function providerValidatePhoneForSuccess(): array
    {
        return [
            ['010-1234-5678'],
            ['01012345678'],
            ['02-123-4567'],
            ['021234567'],
        ];
    }

    /**
     * @dataProvider providerValidatePhoneForFail
     */
    public function testValidatePhoneForFail(string $phone_number): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkPhoneNumber($phone_number, self::MSG);
    }

    public function providerValidatePhoneForFail(): array
    {
        return [
            ['0101-1234-5678'],
            ['010-12341-5678'],
            ['010-1234-56781'],
            ['010-123-56781'],
            ['010123456789'],
            ['310-1234-5678'],
            ['310-1234-a567'],
        ];
    }


    /**
     * @dataProvider providerValidateMailForSuccess
     */
    public function testValidateMailForSuccess(string $mail): void
    {
        ValidationUtils::checkMailAddress($mail, self::MSG);
        $this->assertTrue(true);
    }

    public function providerValidateMailForSuccess(): array
    {
        return [
            ['ridi.ridi@ridi.com.kr'],
            ['ridi_123@ridi.com'],
            ['ridi+-_.ridi@ridi.com'],
            ['ridi.ridi@ridi.com.kr.org'],
            ['xxxx@aa-aaa.co.kr'],
        ];
    }

    /**
     * @dataProvider providerValidateMailForFail
     */
    public function testValidateMailForFail(string $mail): void
    {
        $this->expectException(MsgException::class);
        ValidationUtils::checkMailAddress($mail, self::MSG);
    }

    public function providerValidateMailForFail(): array
    {
        return [
            ['ridi_ridi!#@ridi.com'],
            ['ridi@ridi+.com'],
            ['ridi@ridi_.com'],
        ];
    }
}
