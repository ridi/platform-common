<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Util;

class FileUtils
{
    /**
     * Content-Disposition 으로 내려줄 파일명에 포함될 수 없는 문자를 '_'로 치환한다.
     *
     * @param string $string
     *
     * @return string
     */
    public static function escapeStringForAttachment(string $string): string
    {
        // '/' 와 '\' 가 포함될 수 없다.
        return preg_replace('/[\/\\\\]/', '_', $string);
    }
}
