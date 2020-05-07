<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Super;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Tests\Helper\RequestApi;

class RequestApiTest extends TestCase
{
    /** @dataProvider providerResponse */
    public function test(array $response, array $expected): void
    {
        foreach (['get', 'post', 'put'] as $method) {
            $this->doTest($method, $response, $expected);
        }
    }

    private function doTest(string $method, array $response, array $expected): void
    {
        // body 가 Response 에서 Stream 으로 변환되는 문제로 매번 새로 생성하도록 함 (clone 불가)
        $response = new Response($response['status'], [], $response['body']);
        $handler = new MockHandler([$response]);

        $options = ['handler' => $handler];
        $request_api = new RequestApi('test', 10, 10, $options);
        $response_dto = $request_api->{$method}('test', []);

        $this->assertEquals($expected[0], $response_dto->is_success);
        $this->assertEquals($expected[1], $response_dto->status_code);
        $this->assertEquals($expected[2], $response_dto->response_body);
    }

    public function providerResponse(): array
    {
        return [
            [
                ['status' => 200, 'body' => json_encode(['test' => 1])],
                [true, 200, ['test' => 1]],
            ],
            [
                ['status' => 404, 'body' => json_encode(['error' => 1])],
                [false, 404, ['error' => 1]],
            ],
            [
                ['status' => 500, 'body' => ''],
                [false, 500, ''],
            ],
        ];
    }
}
