<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common;

/** @deprecated  */
class FileLockUtils
{
    private const READ_TIMEOUT_RESOLUTION_MS = 250;
    private const WRITE_TIMEOUT_RESOLUTION_MS = 100;
    private const DEFAULT_LOCK_TIMEOUT = 60;

    private $lock_file_name;
    private $lock_dir;
    private $lock_file;

    public function __construct(string $lock_file_name, string $lock_dir)
    {
        $this->lock_file_name = $lock_file_name;
        $this->lock_dir = $lock_dir;
    }

    public function __destruct()
    {
        if ($this->lock_file) {
            flock($this->lock_file, LOCK_UN);
            @fclose($this->lock_file);
        }
    }

    public function tryReadLock()
    {
        return $this->tryLock(false);
    }

    public function tryWriteLock()
    {
        return $this->tryLock(true);
    }

    private function tryLock(bool $is_exclusive): bool
    {
        if (!is_dir($this->lock_dir)) {
            mkdir($this->lock_dir, 0666, true);
        }

        $this->lock_file = fopen($this->lock_file_name, 'w+');
        @chmod($this->lock_file_name, 0666);
        if ($this->lock_file === false) {
            throw new \RuntimeException('Failed to open lock file');
        }

        $timeout_resolution = $is_exclusive ? self::WRITE_TIMEOUT_RESOLUTION_MS : self::READ_TIMEOUT_RESOLUTION_MS;
        $elapsed_ms = 0;
        $lock_flag = $is_exclusive ? LOCK_EX : LOCK_SH;
        while (!flock($this->lock_file, $lock_flag | LOCK_NB, $is_blocked)) {
            if ($is_blocked && $elapsed_ms <= (self::DEFAULT_LOCK_TIMEOUT * 1000)) {
                usleep($timeout_resolution * 1000);
                $elapsed_ms += $timeout_resolution;
            } else {
                return false;
            }
        }

        return true;
    }
}
