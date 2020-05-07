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
            'case 1. 년월일시분초' => [$date, 'Y-m-d H:i:s', '2020-01-01 11:11:11'],
            'case 2. 년월일시분' => [$date, 'Y-m-d H:i', '2020-01-01 11:11'],
            'case 3. 년월일시' => [$date, 'Y-m-d H', '2020-01-01 11'],
            'case 4. 년월일' => [$date, 'Y-m-d', '2020-01-01'],
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
            'case 1. 경계값 검사 - 1월' => ['2020-01', '2019-12'],
            'case 2. 일반' => ['2020-02', '2020-01'],
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
            'case 1. 경계값 검사 - 12월' => ['2019-12', '2020-01'],
            'case 2. 일반' => ['2020-01', '2020-02'],
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
            $case_name = sprintf('%d월', $i);
            $dates[$case_name] = [sprintf('2020-%d', $i), new \DateTime(sprintf('2020-%d-01', $i))];
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
            'case 1-1. 2020년 테스트 - 1월' => ['2020-01', new \DateTime('2020-01-31')],
            'case 1-2. 2020년 테스트 - 2월' => ['2020-02', new \DateTime('2020-02-29')],
            'case 1-3. 2020년 테스트 - 3월' => ['2020-03', new \DateTime('2020-03-31')],
            'case 1-4. 2020년 테스트 - 4월' => ['2020-04', new \DateTime('2020-04-30')],
            'case 1-5. 2020년 테스트 - 5월' => ['2020-05', new \DateTime('2020-05-31')],
            'case 1-6. 2020년 테스트 - 6월' => ['2020-06', new \DateTime('2020-06-30')],
            'case 1-7. 2020년 테스트 - 7월' => ['2020-07', new \DateTime('2020-07-31')],
            'case 1-8. 2020년 테스트 - 8월' => ['2020-08', new \DateTime('2020-08-31')],
            'case 1-9. 2020년 테스트 - 9월' => ['2020-09', new \DateTime('2020-09-30')],
            'case 1-10. 2020년 테스트 - 10월' => ['2020-10', new \DateTime('2020-10-31')],
            'case 1-11. 2020년 테스트 - 11월' => ['2020-11', new \DateTime('2020-11-30')],
            'case 1-12. 2020년 테스트 - 12월' => ['2020-12', new \DateTime('2020-12-31')],

            'case 2-1. 2월 테스트 - 2016년' => ['2016-02', new \DateTime('2016-02-29')],
            'case 2-2. 2월 테스트 - 2017년' => ['2017-02', new \DateTime('2017-02-28')],
            'case 2-3. 2월 테스트 - 2018년' => ['2018-02', new \DateTime('2018-02-28')],
            'case 2-4. 2월 테스트 - 2019년' => ['2019-02', new \DateTime('2019-02-28')],
            'case 2-5. 2월 테스트 - 2020년' => ['2020-02', new \DateTime('2020-02-29')],
            'case 2-6. 2월 테스트 - 2021년' => ['2021-02', new \DateTime('2021-02-28')],
            'case 2-7. 2월 테스트 - 2022년' => ['2022-02', new \DateTime('2022-02-28')],
            'case 2-8. 2월 테스트 - 2023년' => ['2023-02', new \DateTime('2023-02-28')],
            'case 2-9. 2월 테스트 - 2024년' => ['2024-02', new \DateTime('2024-02-29')],
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
        /** @var \DateTime $execution_time_table */
        foreach ($execution_time_tables as $execution_time_table) {
            $case_name = $execution_time_table->format('H:i');
            $cases[$case_name] = [
                $execution_time_table,
                $time_tables,
                // 처음 실행예정 시각보다 마지막 실행 시각이 이후여야하기 때문에 두번째 조건 설정
                $execution_time_table < $now && $time_tables[0] < $execution_time_table
            ];
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
            'case 1-1. 년월일시분초 vs 년월일시분초' => ['2020-01-01 11:11:11', 'Y-m-d H:i:s', true],
            'case 1-2. 년월일시분초 vs 년월일시분' => ['2020-01-01 11:11:11', 'Y-m-d H:i', false],
            'case 1-3. 년월일시분초 vs 년월일' => ['2020-01-01 11:11:11', 'Y-m-d', false],
            'case 2-1. 년월일시분 vs 년월일시분초' => ['2020-01-01 11:11', 'Y-m-d H:i:s', false],
            'case 2-2. 년월일시분 vs 년월일시분' => ['2020-01-01 11:11', 'Y-m-d H:i', true],
            'case 2-3. 년월일시분 vs 년월일' => ['2020-01-01 11:11', 'Y-m-d', false],
            'case 3-1. 년월일 vs 년월일시분초' => ['2020-01-01', 'Y-m-d H:i:s', false],
            'case 3-2. 년월일 vs 년월일시분' => ['2020-01-01', 'Y-m-d H:i', false],
            'case 3-3. 년월일 vs 년월일' => ['2020-01-01', 'Y-m-d', true],
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
            'case 1. 1일' => ['2020-01-01 00:00:00', '2020-01-02 00:00:00', '1일'],
            'case 2. 1주일 내' => ['2020-01-01 00:00:00', '2020-01-06 00:00:00', '5일'],
            'case 3. 1주' => ['2020-01-01 00:00:00', '2020-01-08 00:00:00', '1주'],
            'case 4. 1주~2주 내' => ['2020-01-01 00:00:00', '2020-01-09 00:00:00', '약 1주'],
            'case 5. 2주' => ['2020-01-01 00:00:00', '2020-01-15 00:00:00', '2주'],
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
            'case 1. 오입력' => ['11:11:11:11', '11:11:11'],
            'case 2. 정상 - 시분초' => ['11:11:11', '11:11:11'],
            'case 3. 정상 - 시분' => ['11:11', '11:11'],
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
            'case 1. 정상' => ['2020-02-01 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', true],
            'case 2. 이후' => ['2020-02-02 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', false],
            'case 3. 이전' => ['2020-01-31 11:11:11', '2020-02-01 00:00:00', '2020-02-02 00:00:00', false],
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
            'case 1-1. "." + AM' => ['2020.01.01 11:11:11 AM', '2020-01-01 11:11:11'],
            'case 1-2. "." + PM' => ['2020.01.01 11:11:11 PM', '2020-01-01 11:11:11'],
            'case 2-1. "." + AM' => ['2020-01.01 11:11:11 AM', '2020-01-01 11:11:11'],
            'case 2-2. "." + PM' => ['2020-01.01 11:11:11 PM', '2020-01-01 11:11:11'],
            'case 3-1. "." - 년월일시분초' => ['2020.01.01 11:11:11', '2020-01-01 11:11:11'],
            'case 3-2. "." - 년월일시분초' => ['2020-01.01 11:11:11', '2020-01-01 11:11:11'],
            'case 4-1. "." - 년월일' => ['2020.01.01', '2020-01-01'],
            'case 4-2. "." - 년월일' => ['2020-01.01', '2020-01-01'],
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
