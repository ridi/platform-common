<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\StringUtils;

class StringUtilsTest extends TestCase
{
    /**
     * @test
     * @dataProvider provider_remain_only_words
     */
    public function remain_only_words(string $str, string $expected): void
    {
        $this->assertEquals($expected, StringUtils::removeSpecificCharaters($str));
    }

    public function provider_remain_only_words(): array
    {
        return [
            ['abcd', 'abcd'],
            ['1234', '1234'],
            ['a' . "\x0", 'a'],
            ['a' . StringUtils::UNICODE_NON_BREAKING_SPACE, 'a'],
            ['a' . StringUtils::UNICODE_ZERO_WIDTH_SPACE, 'a'],
        ];
    }


    /** @test */
    public function remove_tag(): void
    {
        $this->assertEquals('&lt;img&nbsp;src=&quot;blah&quot;&nbsp;/&gt;', StringUtils::removeTag('<img src="blah" />'));
    }

    /**
     * @test
     * @dataProvider provider_jumin_number
     */
    public function jumin_number(string $number, bool $expected): void
    {
        $this->assertEquals($expected, StringUtils::isJumin($number));
    }

    public function provider_jumin_number(): array
    {
        return [
            ['000000-0000000', false],
            ['121312-1212121', false],
            ['121232-1212121', false],
            ['121212-1212121', true],
            ['121212-121212', false],
//            ['121212-12121211', false], // FIXME 길이 테스트 실패
        ];
    }


    /** @test */
    public function jumin_mask(): void
    {
        $this->assertEquals("112233-4******", StringUtils::maskJuminForDisplay("1122334455667"));
    }


    /**
     * @test
     * @dataProvider provider_string_normalize
     */
    public function string_normalize(string $str, bool $is_single_line, string $expected): void
    {
        $this->assertEquals($expected, StringUtils::normalizeString($str, $is_single_line));
    }

    public function provider_string_normalize(): array
    {
        return [
            'case 1. 한줄 테스트 ' => [
                'a' . StringUtils::UNICODE_NON_BREAKING_SPACE . 'b' . StringUtils::UNICODE_NON_BREAKING_SPACE . 'c',
                false, // FIXME is_single_line 이름이 모호해서(한줄로 변경하는 느낌?) 변경 필요
                'a b c',
            ],
            'case 2. 여러줄 테스트' => [
                "a\rb\nc\td" . StringUtils::UNICODE_NON_BREAKING_SPACE . 'e' . StringUtils::UNICODE_NON_BREAKING_SPACE . 'f',
                true,
                'a b c d e f'
            ],
        ];
    }


    /** @test */
    public function remove_double_space(): void
    {
        $this->assertEquals('a b', StringUtils::removeDoubleSpace('a  b'));
        $this->assertEquals('a b', StringUtils::removeDoubleSpace('a       b'));
    }

    /** @test */
    public function remove_special_space(): void
    {
        $this->assertEquals('ab', StringUtils::removeNonBreakingSpace('a' . StringUtils::UNICODE_NON_BREAKING_SPACE . 'b'));
        $this->assertEquals('ab', StringUtils::removeZeroWidthSpace('a' . StringUtils::UNICODE_ZERO_WIDTH_SPACE . 'b'));
    }

    /** @test */
    public function remove_hypen(): void
    {
        $this->assertEquals('ab', StringUtils::removeHyphen('a-b'));
        $this->assertEquals('ab', StringUtils::removeHyphen('a----------b'));
    }


    /**
     * @test
     * @dataProvider provider_xml_to_array
     */
    public function xml_to_array(string $variable, $expected): void
    {
        $this->assertEquals($expected, StringUtils::xml2array($variable));
    }

    public function provider_xml_to_array(): array
    {
        return [
            'case 1. 값만' => [
                '<a>b</a>',
                [0 => 'b'],
            ],
            'case 2. 값 + attribute' => [
                '<a b="c">d</a>',
                [0 => 'd', 'b' => 'c'],
            ],
            // 환경마다 다름..
            /*
            'case 3. 2 depth + attribute' => [
                '<e><a b="c">d</a></e>',
                ['a' => [0 => 'd', 'b' => 'c']],
            ],
            */
        ];
    }


    /** @test */
    public function remove_eng_tags(): void
    {
        $this->assertEquals('<강추> 아스란 연대기', StringUtils::stripTagsOnlyEnglishBegin('<p><강추> 아스란 연대기</p>'));
    }

    /** @test */
    public function comma_separated_string_to_array(): void
    {
        $this->assertEquals(['1', '2', '3', '4'], StringUtils::commaSeparatedToArray('1,2,3,4'));
        $this->assertEquals(['"1"', '"2"', '"3"', '"4"'], StringUtils::commaSeparatedToArray('"1","2","3","4"'));
    }

    /** @test */
    public function line_explode(): void
    {
        $string = "abcd" . PHP_EOL . "efgh";
        $expected = ["abcd", "efgh"];
        $this->assertEquals($expected, StringUtils::explodeByLine($string));
    }

    /** @test */
    public function csv_to_array(): void
    {
        $this->assertEquals([['1', '2', '3', '4']], StringUtils::decodeCsv('1,2,3,4'));
        $this->assertEquals([['1', '2', '3', '4']], StringUtils::decodeCsv('"1","2","3","4"'));
        $this->assertEquals([['1,2', '3', '4']], StringUtils::decodeCsv('"1,2","3","4"'));
        $this->assertEquals([['1"', '2', '3', '4']], StringUtils::decodeCsv('1"",2,3,4'));
    }

    /** @test */
    public function array_to_csv(): void
    {
        $this->assertEquals('"1","2","3","4",' . PHP_EOL, StringUtils::encodeCsv([['1', '2', '3', '4']]));
        $this->assertEquals('"1,2","3","4",' . PHP_EOL, StringUtils::encodeCsv([['1,2', '3', '4']]));
        $this->assertEquals('"1""","2","3","4",' . PHP_EOL, StringUtils::encodeCsv([['1"', '2', '3', '4']]));
    }

    /** @test */
    public function basename(): void
    {
        $this->assertEquals('passwd', StringUtils::basenameUtf8('/etc/passwd'));
        $this->assertEquals('test.sh', StringUtils::basenameUtf8('/home/test/test.sh'));
    }

    /**
     * @test
     * @dataProvider provider_implode_by_chunk
     */
    public function implode_by_chunk(array $data, int $chunk_size, string $expected): void
    {
        $actual = StringUtils::implodeByChunk(';', PHP_EOL, $chunk_size, $data);
        $this->assertEquals($expected, $actual);
    }

    public function provider_implode_by_chunk(): array
    {
        return [
            'case 1. default' => [
                ['A1', 'A2', 'A3', 'B1', 'B2', 'B3'],
                3,
                "A1;A2;A3\nB1;B2;B3",
            ],
            'case 2. 구분자가 데이터에 포함되어 있는 경우' => [
                ['A1;', 'A2', 'A3', 'B1', 'B2', 'B3'],
                3,
                "A1;A2;A3\nB1;B2;B3",
            ],
        ];
    }


    /**
     * @test
     * @dataProvider provider_remain_only_words
     */
    public function remove_unnessary_character(string $str, string $expected): void
    {
        $this->assertEquals($expected, StringUtils::removeUnnecessaryCharacter($str));
    }
}
