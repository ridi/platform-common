<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Cache;

use Predis\Client;

class RedisCache
{
    public const REDIS_CONNECTION_TIME_OUT = 2;

    public const EXPIRE_SECOND = 'EX';
    public const EXPIRE_MILLISECOND = 'PX';

    public const FLAG_KEY_NOT_EXIST = 'NX';
    public const FLAG_KEY_ONLY_EXIST = 'XX';

    /** @var Client|null */
    protected $client;

    public function __construct(array $parameters, array $options)
    {
        try {
            $this->client = new Client($parameters, $options);
        } catch (\Exception $e) {
            $this->client = null;
            error_log($e->getMessage());
        }
    }

    protected function tryToConnect(): void
    {
        try {
            if (!$this->client->isConnected()) {
                $this->client->connect();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function get(string $key): ?string
    {
        try {
            if ($this->client !== null) {
                $this->tryToConnect();

                return $this->client->get($key);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }

    public function getBulk(array $keys): array
    {
        try {
            if ($this->client !== null) {
                $this->tryToConnect();

                $values = $this->client->mget($keys);

                $result = [];
                foreach ($values as $key => $value) {
                    $result[$keys[$key]] = is_null($value) ? null : json_decode($value, true);
                }

                return array_filter($result);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return [];
    }

    public function setJson(string $key, array $value, int $ttl, string $expire_time_mode = self::EXPIRE_SECOND, string $flag = self::FLAG_KEY_NOT_EXIST): void
    {
        $this->set($key, json_encode($value), $ttl, $expire_time_mode, $flag);
    }

    public function set(string $key, string $value, int $ttl, string $expire_time_mode = self::EXPIRE_SECOND, string $flag = self::FLAG_KEY_NOT_EXIST): void
    {
        try {
            if ($this->client !== null) {
                $this->tryToConnect();

                $this->client->set($key, $value, $expire_time_mode, $ttl, $flag);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function expire(string $key, int $ttl): void
    {
        try {
            $this->client->expire($key, $ttl);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public static function makeClientParam(string $alias, string $host, int $port = 6379, int $timeout = self::REDIS_CONNECTION_TIME_OUT): array
    {
        return [
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
            'timeout' => $timeout,
            'alias' => $alias,
        ];
    }
}
