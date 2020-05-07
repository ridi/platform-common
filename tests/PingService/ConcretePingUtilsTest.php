<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\PingService;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Tests\Helper\ConcretePingUtils;

class ConcretePingUtilsTest extends TestCase
{
    /** @dataProvider providerPing */
    public function testPing(array $responses, bool $is_dev, int $expected_http_code): void
    {
        $options = ['handler' => new MockHandler($responses)];
        $result_dict = ConcretePingUtils::ping('/test', $options, $is_dev);
        $this->assertEquals($expected_http_code, $result_dict['http_code']);
    }

    public function providerPing(): array
    {
        return [
            'case 1. 개발모드' => [
                [new Response(200)],
                true,
                0
            ],
            'case 2. 한번에 OK' => [
                [new Response(200)],
                false,
                200
            ],
            'case 3. 실패 후 OK' => [
                [
                    new Response(500),
                    new Response(200),
                ],
                false,
                200
            ],
            'case 4. 최종 실패' => [
                [
                    new Response(500),
                    new Response(500),
                ],
                false,
                500
            ]
        ];
    }
}
