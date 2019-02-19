<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Dto;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

class ResponseDto
{
    /** @var ResponseInterface */
    public $response;
    /** @var array */
    public $response_body;
    /** @var int */
    public $status_code;
    /** @var bool */
    public $is_success;
    /** @var string|null */
    public $json_error_msg;

    public static function importFromResponse(ResponseInterface $response): self
    {
        $dto = new self;

        $dto->response = $response;
        $dto->status_code = $response->getStatusCode();
        $dto->is_success = $response->getStatusCode() === 200;
        if ($response->getBody() instanceof Stream) {
            $response_body = $response->getBody()->getContents();
        } else {
            $response_body = $response->getBody();
        }

        $dto->response_body = json_decode($response_body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $dto->json_error_msg = json_last_error_msg();
        }

        return $dto;
    }
}
