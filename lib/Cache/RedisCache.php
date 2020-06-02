<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Cache;

use Predis\Client;

class RedisCache
{
    public const REDIS_CONNECTION_TIME_OUT = 2;
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

    public function setJson(string $key, array $value, int $ttl): void
    {
        $this->set($key, json_encode($value), $ttl);
    }

    public function set(string $key, string $value, int $ttl): void
    {
        $result = 0;
        try {
            if ($this->client !== null) {
                $this->tryToConnect();

                // setnx()은 호출 시점에서 해당하는 key-value가 존재하지 않는 경우에만 set이 성공한다.
                // set 성공 시 return 1, 실패 시 return 0
                $result = $this->client->setnx($key, $value);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        if ($result === 1) {
            $this->expire($key, $ttl);
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
