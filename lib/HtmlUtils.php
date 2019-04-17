<?php

namespace Ridibooks\Platform\Common;

use Ridibooks\Platform\Common\Exception\MsgException;

class HtmlUtils
{
    public static $cms_allowable_tags = [
        'strong' => [],
        'b' => [],
        'bold' => [],
        'u' => [],
        'br' => [],
        'font' => ['color'],
        'a' => ['href', 'target'],
        'img' => ['src'],
        'video' => ['src'],
        'em' => [],
    ];

    public static $cp_allowable_tags = [
        'strong' => []
    ];

    private static $tags_open_and_close_in_one = [
        'img',
        'video'
    ];

    /**
     * @param $html
     * @param array $allowable_tags
     * @return bool
     */
    public static function isValidHtmlTag($html, array $allowable_tags)
    {
        try {
            self::assertValidHtmlTag($html, $allowable_tags);
        } catch (MsgException $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param $html
     * @param array $allowable_tags
     * @throws MsgException
     */
    public static function assertValidHtmlTag($html, array $allowable_tags)
    {
        $html = self::filterNonAllowableTags($html, $allowable_tags);

        $stack = array();
        preg_replace_callback(
            '/\<(\/?)([a-z]\w*)([^\>]*)\>/i',
            function ($args) use (&$stack) {
                $tag_opened = (strlen($args[1]) == 0);
                $tag = $args[2];
                $attr = $args[3];
                $full_tag = $args[0];

                //br태그는 무시
                if ($tag == 'br') {
                    return;
                }

                if ($tag_opened) {
                    /*
                     * attr의 끝이 / 인것은 무시한다
                     *  - <video src='xxxxx'/>
                     *  - <div />
                     *  - <input />
                     */
                    if ($attr[strlen($attr) - 1] == '/') {
                        return;
                    }
                    array_push($stack, array($tag, $full_tag));
                } else {
                    if (count($stack)) {
                        $last_tags = array_pop($stack);
                        $last_tag = $last_tags[0];
                        $last_full_tag = $last_tags[1];
                    } else {
                        $last_tags = null;
                        $last_tag = null;
                        $last_full_tag = null;
                    }
                    if ($last_tag != $tag) {
                        throw new MsgException(
                            'tag mismatch : ' . $last_full_tag . ' => ' . $full_tag
                        );
                    }
                }
            },
            $html
        );
        if (!empty($stack)) {
            throw new MsgException(
                'tag mismatch : 열리거나 닫히기만 한 태그가 존재합니다.'
            );
        }
    }

    /**
     * 허용하지 않는 태그용 문자를 변환해준다.
     * @param $string
     * @param array $allowable_tags
     * $allowable_tags 배열 형식:
     * $allowable_tags = [
     *         'tag' => ['attr1', 'attr2']
     * ]
     * @return string
     */
    public static function filterNonAllowableTags($string, array $allowable_tags)
    {
        // br태그는 개행으로 자동변경
        $string = preg_replace('/(\<\/?\s*br[^\>]*\>)/is', "\n", $string);

        // 태그 사용을 원천 방지하기 위하여 일단은 무조건 &lt, &gt로 변환
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);

        $offset = 0;
        preg_match_all("/&lt;((?:(?!&lt;|&gt;).)*)&gt;/isU", $string, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $index => $match) {
            $replace = '';

            // 원본 전체 문자열
            $original = $matches[0][$index][0];

            // 내부 문자열
            $tag_and_attr_string = $matches[1][$index][0];

            // attr가 있어야되는 것들과 없어야되는 것들의 분리
            $tags_without_attrs = [];
            $tags_with_attrs = [];
            foreach ($allowable_tags as $tag => $attrs) {
                if (empty($attrs)) {
                    $tags_without_attrs[] = $tag;
                } else {
                    $tags_with_attrs[] = $tag;
                }

                // close 태그는 무조건 attr가 없어야됨
                $tags_without_attrs[] = '/' . $tag;
            }

            // attr가 없어야만 되는 것들
            if (in_array(strtolower(trim($tag_and_attr_string)), $tags_without_attrs)) {
                $replace = trim($tag_and_attr_string);
            }

            // attr가 필수적인것들
            $is_matched = !!preg_match("/^(" . implode('|', $tags_with_attrs) .")\s+(.+)$/is", trim($tag_and_attr_string), $sub_matches);
            if ($is_matched) {
                // 태그명
                $tag_name = $sub_matches[1];

                // attr 문자열 필터링
                $attribute_string = $sub_matches[2];
                $attributes = self::extractTagAttributes($attribute_string);
                $filtered_attributes = self::filterAllowableTagAttributes($tag_name, $attributes);
                $replace_attribute_string = self::implodeTagAttributes($filtered_attributes);
                if (!StringUtils::isEmpty($replace_attribute_string)) {
                    $replace = $tag_name . ' ' . $replace_attribute_string;

                    if (in_array(strtolower($tag_name), self::$tags_open_and_close_in_one)) {
                        $replace .= '/';
                    }
                }
            }

            if (!StringUtils::isEmpty($replace)) {
                $replace = '<' . $replace . '>';

                // 문자열 새로 조합
                $original_string_offset = $matches[0][$index][1];
                $pre_string = substr($string, 0, $offset + $original_string_offset);
                $post_string = substr($string, $offset + $original_string_offset + strlen($original), strlen($string) - ($offset + $original_string_offset + strlen($original)));
                $string = $pre_string . $replace . $post_string;

                // 원본 문자열에서 구한 offset과 변환된 문자열의 offset과 달라지는 점을 고려
                $offset += strlen($replace) - strlen($original);
            }
        }

        return $string;
    }

    private static function extractTagAttributes($string)
    {
        $html_attributes = array();
        preg_match_all("/([^=\s]+)\s*=\s*[\"']*((?:.(?![\"']*\s+(?:\S+)=|[>\"']))+.)[\"']*/is", htmlspecialchars_decode(stripslashes($string)), $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $attr = $match[1];
            $value = $match[2];
            $html_attributes[$attr] = $value;
        }

        return $html_attributes;
    }

    private static function filterAllowableTagAttributes($tag_name, $html_attributes)
    {
        // 문자열비교시 대소문자를 가리기때문에, 소문자로 해당값 입력
        $allowable_attributes = array(
            'a' => array('href', 'target'),
            'font' => array('color'),
            'img' => array('src'),
            'video' => array('src')
        );

        $filtered_html_attributes = array();
        foreach ($html_attributes as $attr => $value) {
            if (!in_array(strtolower($attr), (array)$allowable_attributes[strtolower($tag_name)])) {
                continue;
            }

            $filtered_html_attributes[$attr] = $value;
        }

        return $filtered_html_attributes;
    }

    private static function implodeTagAttributes($html_attributes)
    {
        $attr_values = array();
        foreach ($html_attributes as $attr => $value) {
            $attr_values[] = $attr . '="' . $value . '"';
        }

        return implode(' ', $attr_values);
    }

    /**
     * 허용하지 않는 태그용 문자를 제거해준다.
     * @param $string
     * @param array $allowable_tags
     * $allowable_tags 배열 형식:
     * $allowable_tags = [
     *         'tag' => ['attr1', 'attr2']
     * ]
     * @return string
     */
    public static function stripNonAllowableTags($string, $allowable_tags)
    {
        $string = self::filterNonAllowableTags($string, $allowable_tags);
        $string = StringUtils::swapTwoSubStrings($string, '<', '&lt;');
        $string = StringUtils::swapTwoSubStrings($string, '>', '&gt;');
        $string = strip_tags($string);
        $string = StringUtils::swapTwoSubStrings($string, '<', '&lt;');
        $string = StringUtils::swapTwoSubStrings($string, '>', '&gt;');

        return $string;
    }
}
