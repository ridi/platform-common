<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\ArrayUtils;

class ArrayUtilsTest extends TestCase
{
    /**
     * @dataProvider providerArrayDiffRecursively
     */
    public function testGetArrayDiffRecursively(array $arr1, array $arr2, array $expected): void
    {
        $this->assertEquals($expected, ArrayUtils::getArrayDiffRecursively($arr1, $arr2));
    }

    public function providerArrayDiffRecursively(): array
    {
        return [
            'case 1. 1차원 배열' => [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                ['a' => 0, 'b' => 2, 'c' => 3, 'e' => 4],
                ['a' => 1, 'd' => 4],
            ],
            'case 2. 2차원 배열' => [
                ['test' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4] ],
                ['test' => ['a' => 0, 'b' => 2, 'c' => 3, 'e' => 4], 'test2' => [] ],
                ['test' => ['a' => 1, 'd' => 4] ],
            ],
            'case 3. 3차원 배열' => [
                ['test' => ['a' => [ 'f' => 1 ], 'b' => 2, 'c' => 3, 'd' => 4] ],
                ['test' => ['a' => [ 'f' => 0 ], 'b' => 2, 'c' => 3, 'e' => 4] ],
                ['test' => ['a' => [ 'f' => 1 ], 'd' => 4] ],
            ],
            'case 4. type check' => [
                ['a' => 1, 'b' => false, 'c' => true, 'd' => '1', 'e' => null, 'f' => 0.0, 'g' => (object)[] ],
                ['a' => 1, 'b' => false, 'c' => true, 'd' => '1', 'e' => null, 'f' => 0.0, 'g' => (object)[] ],
                [],
            ],
        ];
    }


    /**
     * @dataProvider providerJoinDicts
     */
    public function testJoinDicts(
        array $left_dicts,
        array $right_dicts,
        int $left_dicts_column_index,
        int $right_dicts_column_index,
        array $expected
    ): void {
        $this->assertEquals(
            $expected,
            ArrayUtils::joinDicts($left_dicts, $right_dicts, $left_dicts_column_index, $right_dicts_column_index)
        );
    }

    public function providerJoinDicts(): array
    {
        return [
            'case 1. 일반 join' => [
                [ ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4] ],
                [ ['a' => 1, 'e' => 2, 'f' => 3, 'g' => 4] ],
                0,
                0,
                [ ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 2, 'f' => 3, 'g' => 4] ],
            ],
            'case 2. key 중복 테스트' => [
                [ ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4] ],
                [ ['a' => 1, 'b' => 9, 'f' => 3, 'g' => 4] ],
                0,
                0,
                [ ['a' => 1, 'b' => 9, 'c' => 3, 'd' => 4, 'f' => 3, 'g' => 4] ],
            ],
            'case 3. key 순서 다를때 테스트' => [
                [ ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4] ],
                [ ['g' => 1, 'f' => 9, 'b' => 3, 'a' => 4] ],
                0,
                0,
                [ ['a' => 4, 'b' => 3, 'c' => 3, 'd' => 4, 'g' => 1, 'f' => 9] ],
            ],
        ];
    }


    /**
     * @dataProvider providerConvertLinksToSets
     */
    public function testConvertLinksToSets(array $from_list, array $to_list, array $expected): void
    {
        $this->assertEquals($expected, ArrayUtils::convertLinksToSets($from_list, $to_list));
    }

    public function providerConvertLinksToSets(): array
    {
        return [
            'case 1' => [
                [1,2,4,4,5],
                [4,4,3,1,6],
                [
                    [1,4,2,5 => 3], // FIXME key 제거 필요
                    [5,6],
                ],
            ],
            'case 2' => [
                [1,2,3,4,5,6],
                [6,5,4,3,2,1],
                [
                    [1,6],
                    [2,5],
                    [3,4],
                ],
            ],
        ];
    }


    public function testArrayFilterByKey(): void
    {
        $actual = ArrayUtils::arrayFilterByKey(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4], ['a', 'b']);
        $this->assertEquals(['a' => 1, 'b' => 2], $actual);
    }

    public function testConvertValuesToKey(): void
    {
        $actual = ArrayUtils::convertValuesToKey(['a', 'b']);
        $this->assertEquals(['a' => 'a', 'b' => 'b'], $actual);
    }

    public function testParseArray(): void
    {
        $this->assertEquals(['a'], ArrayUtils::parseArray('a'));
        $this->assertEquals([], ArrayUtils::parseArray(null));
        $this->assertEquals(['a'], ArrayUtils::parseArray(['a']));
    }


    /**
     * @dataProvider providerShortenSequential
     */
    public function testShortenSequential(array $value, string $glue, array $expected): void
    {
        $this->assertEquals($expected, ArrayUtils::shortenSequential($value, $glue));
    }

    public function providerShortenSequential(): array
    {
        return [
            'case 1. 오름차순' => [
                [1,2,3,5,6,8],
                '~',
                ['1~3', '5~6', '8'],
            ],
            'case 2. 내림차순' => [
                [8,6,5,3,2,1],
                '-',
                ['1-3', '5-6', '8'],
            ],
        ];
    }

    public function testExcludeNull(): void
    {
        $actual = ArrayUtils::excludeNull(['a' => 1, 'b' => 2, 'c' => [null], 'd' => null]);
        $expected = ['a' => 1, 'b' => 2, 'c' => [null]];
        $this->assertEquals($expected, $actual);
    }
}
