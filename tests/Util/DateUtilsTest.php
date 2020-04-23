<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\DateUtils;

class DateUtilsTest extends TestCase
{
    public function testNow(): void
    {
        $now = DateUtils::getNow();
        $this->assertNotFalse(date_create($now));
    }

    public function testGetWeekNumberOfMonth(): void
    {
        $first_day_of_year = new \DateTime('2020-01-01 00:00:00');
        $first_week_of_month = DateUtils::getWeekNumberOfMonth($first_day_of_year);
        $this->assertEquals(1, $first_week_of_month);
    }


    /**
     * @dataProvider providerFormattedDate
     */
    public function testGetFormattedDate(string $date, string $format, string $expected): void
    {
        $formatted_date = DateUtils::getFormattedDate($date, $format);
        $this->assertEquals($expected, $formatted_date);
    }

    public function providerFormattedDate(): array
    {
        $date = '2020-01-01 11:11:11';

        return [
            [$date, 'Y-m-d H:i:s', '2020-01-01 11:11:11'],
            [$date, 'Y-m-d H:i', '2020-01-01 11:11'],
            [$date, 'Y-m-d H', '2020-01-01 11'],
            [$date, 'Y-m-d', '2020-01-01'],
        ];
    }


    /**
     * @dataProvider providerPrevYearmonth
     */
    public function testGetPrevYearmonth(string $year_month, string $expected): void
    {
        $prev_year_month = DateUtils::getPrevYearmonth($year_month);
        $this->assertEquals($expected, $prev_year_month);
    }

    public function providerPrevYearmonth(): array
    {
        return [
            ['2020-01', '2019-12'],
            ['2020-02', '2020-01'],
        ];
    }


    /**
     * @dataProvider providerNextYearmonth
     */
    public function testGetNextYearmonth(string $year_month, string $expected): void
    {
        $next_year_month = DateUtils::getNextYearmonth($year_month);
        $this->assertEquals($expected, $next_year_month);
    }

    public function providerNextYearmonth(): array
    {
        return [
            ['2019-12', '2020-01'],
            ['2020-01', '2020-02'],
        ];
    }


    /**
     * @dataProvider providerFirstdayOfYearmonth
     */
    public function testGetFirstdayOfYearmonth(string $year_month, \DateTime $expected): void
    {
        $first_day_of_month = DateUtils::getFirstdayOfYearmonth($year_month);
        $this->assertEquals($expected, $first_day_of_month);
    }

    public function providerFirstdayOfYearmonth(): array
    {
        $dates = [];
        for ($i = 1; $i <= 12; $i++) {
            $dates[] = [sprintf('2020-%d', $i), new \DateTime(sprintf('2020-%d-01', $i))];
        }

        return $dates;
    }


    /**
     * @dataProvider providerLastdayOfYearmonth
     */
    public function testGetLastdayOfYearmonth(string $year_month, \DateTime $expected): void
    {
        $last_day_of_month = DateUtils::getLastdayOfYearmonth($year_month);
        $this->assertEquals($expected, $last_day_of_month);
    }

    public function providerLastdayOfYearmonth(): array
    {
        return [
            ['2020-01', new \DateTime('2020-01-31')],
            ['2020-02', new \DateTime('2020-02-29')],
            ['2020-03', new \DateTime('2020-03-31')],
            ['2020-04', new \DateTime('2020-04-30')],
            ['2020-05', new \DateTime('2020-05-31')],
            ['2020-06', new \DateTime('2020-06-30')],
            ['2020-07', new \DateTime('2020-07-31')],
            ['2020-08', new \DateTime('2020-08-31')],
            ['2020-09', new \DateTime('2020-09-30')],
            ['2020-10', new \DateTime('2020-10-31')],
            ['2020-11', new \DateTime('2020-11-30')],
            ['2020-12', new \DateTime('2020-12-31')],

            // 2월 테스트
            ['2016-02', new \DateTime('2016-02-29')],
            ['2017-02', new \DateTime('2017-02-28')],
            ['2018-02', new \DateTime('2018-02-28')],
            ['2019-02', new \DateTime('2019-02-28')],
            ['2020-02', new \DateTime('2020-02-29')],
            ['2021-02', new \DateTime('2021-02-28')],
            ['2022-02', new \DateTime('2022-02-28')],
            ['2023-02', new \DateTime('2023-02-28')],
            ['2024-02', new \DateTime('2024-02-29')],
        ];
    }


    public function testIsToday(): void
    {
        $now = new \DateTime();
        $this->assertTrue(DateUtils::isToday($now));
    }

    public function testConvertToStartDateTime(): void
    {
        $converted_datetime = DateUtils::convertToStartDateTime('2020-01-01');
        $this->assertEquals('2020-01-01 00:00:00', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));

        $converted_datetime = DateUtils::convertToStartDateTime('2020-01-01 11:11:11');
        $this->assertEquals('2020-01-01 11:11:11', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));

        $converted_datetime = DateUtils::convertToStartDateTime('2020-01-01 11:11');
        $this->assertEquals('2020-01-01 11:11:00', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));
    }

    public function testConvertToEndDateTime(): void
    {
        $converted_datetime = DateUtils::convertToEndDateTime('2020-01-01');
        $this->assertEquals('2020-01-01 23:59:59', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));

        $converted_datetime = DateUtils::convertToEndDateTime('2020-01-01 11:11:11');
        $this->assertEquals('2020-01-01 11:11:11', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));

        $converted_datetime = DateUtils::convertToEndDateTime('2020-01-01 11:11');
        $this->assertEquals('2020-01-01 11:11:00', $converted_datetime->format(DateUtils::DEFAULT_DATETIME_FORMAT));
    }


    /**
     * @dataProvider providerIsExecutionTimeInTimeTable
     */
    public function testIsExecutionTimeInTimeTable(?\DateTime $test_datetime, array $time_tables, bool $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::isExecutionTimeInTimeTable($test_datetime, $time_tables));
    }

    public function providerIsExecutionTimeInTimeTable(): array
    {
        $time_tables = [
            (new \DateTime())->setTime(11, 30),
            (new \DateTime())->setTime(12, 00),
            (new \DateTime())->setTime(16, 30),
            (new \DateTime())->setTime(17, 00),
        ];
        $execution_time_tables = [
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(11, 45),
            (new \DateTime())->setTime(14, 0),
            (new \DateTime())->setTime(16, 45),
            (new \DateTime())->setTime(18, 0),
        ];

        $now = DateUtils::createNowExceptMicroseconds();
        $cases = [];
        foreach ($execution_time_tables as $excution_time_table) {
            $cases[] = [$excution_time_table, $time_tables, $excution_time_table < $now];
        }

        return $cases;
    }


    /**
     * @dataProvider providerValidateDateTime
     */
    public function testValidateDateTime(string $datetime, string $format, bool $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::validateDateTime($datetime, $format));
    }

    public function providerValidateDateTime(): array
    {
        return [
            ['2020-01-01 11:11:11', 'Y-m-d H:i:s', true],
            ['2020-01-01 11:11:11', 'Y-m-d H:i', false],
            ['2020-01-01 11:11:11', 'Y-m-d', false],
            ['2020-01-01 11:11', 'Y-m-d H:i:s', false],
            ['2020-01-01 11:11', 'Y-m-d H:i', true],
            ['2020-01-01 11:11', 'Y-m-d', false],
            ['2020-01-01', 'Y-m-d H:i:s', false],
            ['2020-01-01', 'Y-m-d H:i', false],
            ['2020-01-01', 'Y-m-d', true],
        ];
    }


    /**
     * @dataProvider providerConvertDateDiffToKoreanUnit
     */
    public function testConvertDateDiffToKoreanUnit(string $start_date, string $end_date, string $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::convertDateDiffToKoreanUnit($start_date, $end_date));
    }

    public function providerConvertDateDiffToKoreanUnit(): array
    {
        return [
            ['2020-01-01 00:00:00', '2020-01-02 00:00:00', '1일'],
            ['2020-01-01 00:00:00', '2020-01-06 00:00:00', '5일'],
            ['2020-01-01 00:00:00', '2020-01-08 00:00:00', '1주'],
            ['2020-01-01 00:00:00', '2020-01-09 00:00:00', '약 1주'],
            ['2020-01-01 00:00:00', '2020-01-15 00:00:00', '2주'],
        ];
    }


    /**
     * @dataProvider providerFixInsertedWrongTime
     */
    public function testFixInsertedWrongTime(string $time, string $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::fixInsertedWrongTime($time));
    }

    public function providerFixInsertedWrongTime(): array
    {
        return [
            ['11:11:11:11', '11:11:11'],
            ['11:11:11', '11:11:11'],
            ['11:11', '11:11'],
        ];
    }


    /**
     * @dataProvider providerCheckInPeriod
     */
    public function testCheckInPeriod(string $check_date, string $start_date, string $end_date, bool $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::checkInPeriod($check_date, $start_date, $end_date));
    }

    public function providerCheckInPeriod(): array
    {
        return [
            ['2020-02-01 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', true],
            ['2020-02-02 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', false],
            ['2020-01-31 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', false],
        ];
    }


    /**
     * @dataProvider providerNormalizeDateTimeString
     */
    public function testNormalizeDateTimeString(string $date_time, string $exptected): void
    {
        $this->assertEquals($exptected, DateUtils::normalizeDateTimeString($date_time));
    }

    public function providerNormalizeDateTimeString(): array
    {
        // 0000.00.00 => 0000-00-00
        // 0000-00.00  00:00:00 => 0000-00-00 00:00:00
        // 0000-00-00 00:00:00 AM => 0000-00-00 00:00:00

        return [
            ['2020.01.01 11:11:11 AM', '2020-01-01 11:11:11'],
            // ['2020.01.01 11:11:11 PM', '2020-01-01 23:11:11'], // FIXME error
            ['2020-01.01 11:11:11 AM', '2020-01-01 11:11:11'],
            // ['2020-01.01 11:11:11 PM', '2020-01-01 23:11:11'], // FIXME error
            ['2020.01.01 11:11:11', '2020-01-01 11:11:11'],
            ['2020-01.01 11:11:11', '2020-01-01 11:11:11'],
            ['2020.01.01', '2020-01-01'],
            ['2020-01.01', '2020-01-01'],
        ];
    }


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
