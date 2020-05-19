<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Cache;

use Ridibooks\Platform\Common\Cache\Dto\RedisConfigDto;
use Ridibooks\Platform\Common\Util\StringUtils;

class RedisHelper
{
    public static function getRedisClientConfigs(?string $master_host, array $slave_hosts): ?RedisConfigDto
    {
        // Redis Cache Client
        $hosts = [];
        if (StringUtils::isEmpty($master_host)) {
            return null;
        }

        $hosts['master'] = RedisCache::makeClientParam('master', $master_host);

        foreach ($slave_hosts as $index => $slave_host) {
            if (!StringUtils::isEmpty($slave_host)) {
                $slave_host_name = 'slave-' . $index;
                $hosts[$slave_host_name] = RedisCache::makeClientParam($slave_host_name, $slave_host);
            }
        }

        $options = [];
        if (!empty($hosts) && isset($hosts['master'])) {
            if (count($hosts) > 1) {
                $options = ['replication' => true];
            }
        }

        return RedisConfigDto::import($hosts, $options);
    }
}
