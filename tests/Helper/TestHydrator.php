<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Helper;

use Mailgun\Exception\HydrationException;
use Mailgun\Hydrator\Hydrator;
use Mailgun\Model\ApiResponse;
use Mailgun\Model\EmailValidation\ValidateResponse;
use Mailgun\Model\Message\SendResponse;
use Psr\Http\Message\ResponseInterface;

class TestHydrator implements Hydrator
{
    public function hydrate(ResponseInterface $response, string $class)
    {
        $body = $response->getBody()->__toString();

        // test에서는 content-type 이 맞지 않음
        // $content_type = $response->getHeaderLine('Content-Type');

        $data = $this->generateSampleData($class, $body);

        if (is_subclass_of($class, ApiResponse::class)) {
            $object = call_user_func($class.'::create', $data);
        } else {
            $object = new $class($data);
        }

        return $object;
    }

    private function generateSampleData(string $class, $body): array
    {
        switch ($class) {
            case SendResponse::class:
                // example
                // {"message":"Post received. Thanks!"}

                $data = json_decode($body, true);

                if (JSON_ERROR_NONE !== json_last_error()) {
                    throw new HydrationException(sprintf('Error (%d) when trying to json_decode response', json_last_error()));
                }

                if (!empty($data['message'])) {
                    $data['id'] = 'ok';
                }
                break;
            case ValidateResponse::class:
                $data = ['is_valid' => true];
                break;
            default:
                throw new HydrationException(sprintf('Not support class - %s', $class));
        }

        return $data;
    }
}
