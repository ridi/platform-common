<?php
namespace Ridibooks\Platform\Common\Cache;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * 테스트 환경에서는 ArrayCache, 서버 환경에서는 ApcCache -> FilesystemCache 순으로 시도한다.
 */
class AdaptableCache extends CacheProvider
{
    /**
     * @var CacheProvider
     */
    private $impl;

    public function __construct()
    {
        if (defined('PHPUNIT')) {
            $this->impl = new ArrayCache();
        } elseif (extension_loaded('apcu')) {
            // PHP 5.x의 APCu는 APC와 호환 지원, PHP 7은 APCu만 사용해야 하고 APC와 호환되지 않음.
            $this->impl = new ApcuCache();
        } else {
            $this->impl = new FilesystemCache(sys_get_temp_dir());
        }
    }

    protected function doFetch($id)
    {
        return $this->impl->doFetch($id);
    }

    protected function doContains($id)
    {
        return $this->impl->doContains($id);
    }

    protected function doSave($id, $data, $lifeTime = 0)
    {
        return $this->impl->doSave($id, $data, $lifeTime);
    }

    protected function doDelete($id)
    {
        return $this->impl->doDelete($id);
    }

    protected function doFlush()
    {
        return $this->impl->doFlush();
    }

    protected function doGetStats()
    {
        return $this->impl->doGetStats();
    }
}
