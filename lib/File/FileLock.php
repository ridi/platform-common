<?php
namespace Ridibooks\Platform\Common\File;

/**
 * FileLock 인스턴스가 소멸되면 PHP GC가 lock까지 회수하므로 주의
 */
class FileLock
{
    private $lock_file_path;
    private $lock_file_name;
    private $lock_file;
    private $is_has_lock = false;
    private $is_delete_lock_file;

    public function __construct($name, $is_delete_lock_file = false)
    {
        $filtered_name = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->lock_file_name = $filtered_name . '.lock';
        $this->is_delete_lock_file = $is_delete_lock_file;
    }

    /**
     * lock 얻고 실패하면 throw
     * @throws \RuntimeException
     */
    public function lock()
    {
        $this->tryLock();

        if (!$this->isHasLock()) {
            throw new \RuntimeException($this->lock_file_name . ' already exists, exiting');
        }
    }

    /**
     * lock 얻기 시도하고 성공하면 true, 실패하면 false
     * @return bool
     */
    public function tryLock()
    {
        $this->lock_file_path = sys_get_temp_dir() . '/' . $this->lock_file_name;
        $this->lock_file = fopen($this->lock_file_path, 'w+');
        $this->is_has_lock = flock($this->lock_file, LOCK_EX | LOCK_NB);

        return $this->is_has_lock;
    }

    public function unlock()
    {
        if (!$this->isHasLock()) {
            return;
        }

        flock($this->lock_file, LOCK_UN);
        @fclose($this->lock_file);
        if ($this->is_delete_lock_file) {
            @unlink($this->lock_file_path);
        }

        $this->is_has_lock = false;
    }

    /**
     * @return bool 현재 lock을 가지고 있는지 여부
     */
    public function isHasLock()
    {
        return $this->is_has_lock;
    }
}
