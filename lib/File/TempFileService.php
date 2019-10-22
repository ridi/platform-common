<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\File;

class TempFileService
{
    private $files = [];
    private static $instance;

    private function __construct()
    {
    }

    public function __destruct()
    {
        foreach ($this->files as $temp_file) {
            @unlink($temp_file);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getTempFile(string $prefix): string
    {
        $temp_file = tempnam(sys_get_temp_dir(), $prefix);
        $this->addAutoRemoveQueue($temp_file);

        return $temp_file;
    }

    public function getTempFileWithPostfix(string $prefix, string $postfix): string
    {
        $temp_file = $this->getTempFile($prefix);

        //tempnam 으로 파일 생성시 postfix 를 붙일 수 없기 때문에 없다는 가정 하에 진행
        $temp_file_with_postfix = $temp_file . $postfix;
        $this->addAutoRemoveQueue($temp_file_with_postfix);

        return $temp_file_with_postfix;
    }

    public function addAutoRemoveQueue(string $file_path): void
    {
        $this->files[] = $file_path;
    }
}
