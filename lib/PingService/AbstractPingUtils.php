<?php

namespace Ridibooks\Platform\Common\PingService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPingUtils
{
    public static function ping(string $path): array
    {
        if (\Config::$UNDER_DEV) {
            return ['http_code' => 0, 'error' => 'under_dev', 'body' => false];
        }

        $url = static::getBaseUrl() . $path;

        $retry_count = 0;
        $result_dict = [];
        while ($retry_count <= static::getMaxRetryCount()) {
            $result_dict = self::getContents($url);
            if ($result_dict['http_code'] === Response::HTTP_OK) {
                return $result_dict;
            }
            sleep(static::getRetryWaitSeconds());
            $retry_count++;
        }

        return $result_dict;
    }

    /**
     * @param string $url
     *
     * @return array 호출 결과 [http_code, error, body]
     */
    private static function getContents(string $url): array
    {
        $option = [
            'timeout' => static::getTimeoutSeconds(),
            'connect_timeout' => static::getConnectionTimeoutSeconds(),
        ];

        $client = new Client($option);

        try {
            $response = $client->get($url);
            $status_code = $response->getStatusCode();
            $body = $response->getBody();
        } catch (ConnectException $e) {
            $status_code = $e->getCode();
            $body = $e->getMessage();
        }

        return ['http_code' => $status_code, 'body' => $body];
    }

    abstract protected static function getBaseUrl(): string;

    protected static function getMaxRetryCount(): int
    {
        return 10;
    }

    protected static function getRetryWaitSeconds(): int
    {
        return 6;
    }

    protected static function getTimeoutSeconds(): int
    {
        return 10;
    }

    protected static function getConnectionTimeoutSeconds(): int
    {
        return 10;
    }
}
