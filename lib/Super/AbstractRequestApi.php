<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Super;

use GuzzleHttp\Client;
use Ridibooks\Platform\Common\Dto\ResponseDto;

abstract class AbstractRequestApi
{
    public const POST_FORM_PARAMS = 'form_params';
    public const POST_JSON = 'json';

    protected $client;

    public function __construct(string $base_url, int $timeout, int $connect_timeout, array $options = [])
    {
        $default = [
            'base_uri' => $base_url,
            'timeout' => $timeout,
            'connect_timeout' => $connect_timeout,
        ];

        $this->client = new Client(
            array_merge($default, $options)
        );
    }

    public function post(string $url, array $params, array $headers = []): ResponseDto
    {
        $options = $params;

        if (!empty($headers)) {
            $options = array_merge($options, $headers);
        }

        $response = $this->client->post($url, $options);

        return ResponseDto::importFromResponse($response);
    }

    public function get(string $url, array $params = [], array $headers = []): ResponseDto
    {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->client->get($url, $headers);

        return ResponseDto::importFromResponse($response);
    }

    public function put(string $url, array $headers = []): ResponseDto
    {
        $response = $this->client->put($url, $headers);

        return ResponseDto::importFromResponse($response);
    }

    protected function getBaseHeaders(array $headers = []): array
    {
        $options = [];
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        return $options;
    }
}
