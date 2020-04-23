<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\HtmlUtils;

class HtmlUtilsTest extends TestCase
{
    public function testFilterNonAllowableTags(): void
    {
        // 닫는 태그가 별도로 없는 경우
        // attribute 를 허용하지 않는 경우
        $allowable_tags = ['img' => []];
        $actual = HtmlUtils::filterNonAllowableTags('<img id="test" class="test" />', $allowable_tags);
        $expected = '&lt;img id="test" class="test" /&gt;';
        $this->assertEquals($expected, $actual);

        // attribute가 미리 선언된 형식과 같은 경우
        $allowable_tags = ['img' => ['src']];
        $actual = HtmlUtils::filterNonAllowableTags('<img src="test"/>', $allowable_tags);
        $expected = '<img src="test"/>';
        $this->assertEquals($expected, $actual);

        // attribute가 미리 선언된 형식과 다른 경우
        $allowable_tags = ['img' => ['src']];
        $actual = HtmlUtils::filterNonAllowableTags('<img src="test" alt="test"/>', $allowable_tags);
        $expected = '<img src="test"/>';
        $this->assertEquals($expected, $actual);

        // FIXME 결과가 이상함
        // 닫는 태그가 별도로 있는 경우
        // attribute 를 허용하지 않는 경우
        $allowable_tags = ['div' => []];
        $actual = HtmlUtils::filterNonAllowableTags('<div id="test" class="test">test</div>', $allowable_tags);
        $expected = '&lt;div id="test" class="test"&gt;test</div>';
        $this->assertEquals($expected, $actual);

        // attribute가 미리 선언된 형식과 같은 경우
        $allowable_tags = ['div' => ['class']];
        $actual = HtmlUtils::filterNonAllowableTags('<div class="test">test</div>', $allowable_tags);
        $expected = '&lt;div class="test"&gt;test</div>';
        $this->assertEquals($expected, $actual);

        // attribute가 미리 선언된 형식과 다른 경우
        $allowable_tags = ['div' => ['class']];
        $actual = HtmlUtils::filterNonAllowableTags('<div id="test" class="test">test</div>', $allowable_tags);
        $expected = '&lt;div id="test" class="test"&gt;test</div>';
        $this->assertEquals($expected, $actual);
    }
}
