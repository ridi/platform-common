<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Util;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Util\RequestUtils;

class RequestUtilsTest extends TestCase
{
    /**
     * @test
     * @dataProvider provider_request
     */
    public function get_content_from_request(Request $request, array $expected): void
    {
        $request_content = RequestUtils::getContent($request);
        $this->assertEquals($expected, $request_content);
    }

    public function provider_request(): array
    {
        $data = [
            'data' => [
                'param1' => 'data',
                'param2' => 123,
            ]
        ];

        $get_request = Request::create('', Request::METHOD_GET, $data);
        $post_request_not_json = Request::create('', Request::METHOD_POST, $data);

        $post_request_json = Request::create('', Request::METHOD_POST, [], [], [], [], json_encode($data));
        $post_request_json->headers->set('Content-Type', 'application/json');

        return [
            'case 1. GET Request' => [$get_request, $data],
            'case 2. POST - NOT JSON' => [$post_request_not_json, $data],
            'case 3. POST - JSON' => [$post_request_json, $data],
        ];
    }
}
