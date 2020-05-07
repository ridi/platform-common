<?php

namespace Ridibooks\Platform\Common\PingService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPingUtils
{
    /** @var string */
    private $path;
    /** @var bool */
    private $is_dev;
    /** @var array */
    private $options;

    public static function ping(string $path, array $options = [], bool $is_dev = false): array
    {
        return (new static($path, $options, $is_dev))->pingWithRetry();
    }

    private function __construct(string $path, array $options, bool $is_dev)
    {
        $this->path = $path;
        $this->options = $options;
        $this->is_dev = $is_dev;
    }

    private function pingWithRetry(): array
    {
        if ($this->is_dev) {
            return ['http_code' => 0, 'error' => 'under_dev', 'body' => false];
        }

        $url = $this->getBaseUrl() . $this->path;

        $retry_count = 0;
        $result_dict = [];
        while ($retry_count <= $this->getMaxRetryCount()) {
            $result_dict = $this->getContents($url);
            if ($result_dict['http_code'] === Response::HTTP_OK) {
                return $result_dict;
            }
            sleep($this->getRetryWaitSeconds());
            $retry_count++;
        }

        return $result_dict;
    }

    /**
     * @param string $url
     *
     * @return array 호출 결과 [http_code, body]
     */
    private function getContents(string $url): array
    {
        $option = [
            'timeout' => $this->getTimeoutSeconds(),
            'connect_timeout' => $this->getConnectionTimeoutSeconds(),
        ];

        $client = new Client(array_merge($this->options, $option));

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

    abstract protected function getBaseUrl(): string;

    protected function getMaxRetryCount(): int
    {
        return 10;
    }

    protected function getRetryWaitSeconds(): int
    {
        return 6;
    }

    protected function getTimeoutSeconds(): int
    {
        return 10;
    }

    protected function getConnectionTimeoutSeconds(): int
    {
        return 10;
    }
}
