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

    public static function rmdirRecursively($dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $target = $dir . "/" . $object;
                    if (is_dir($target)) {
                        self::rmdirRecursively($target);
                    } else {
                        unlink($target);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public static function renameFile(string $src_path, string $dest_path): bool
    {
        if (!self::isSameScheme($src_path, $dest_path)) {
            // 프로토콜이 다르면 dest로 file copy 후 원본 삭제
            $copy_result = copy($src_path, $dest_path);

            if (!$copy_result) {
                return false;
            }

            return unlink($src_path);
        }

        return rename($src_path, $dest_path);
    }

    public static function isSameScheme(string $src_path, string $dest_path): bool
    {
        $src_parsed_url = parse_url($src_path);
        $dest_parsed_url = parse_url($dest_path);

        return $src_parsed_url['scheme'] === $dest_parsed_url['scheme'];
    }

    public static function isS3Scheme(string $path): bool
    {
        $parsed_url = parse_url($path);

        return preg_match_all('/(s3|S3)/', $parsed_url['scheme']) !== false;
    }
}
