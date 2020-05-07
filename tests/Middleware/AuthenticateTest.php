<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Auth\JwtUtils;
use Ridibooks\Platform\Common\Middleware\Authenticate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticateTest extends TestCase
{
    private const ISS = 'test';
    private const AUD = 'test';

    private $public_file;
    private $private_file;

    private $authenticate;

    public function setUp(): void
    {
        $this->public_file = file_get_contents(__DIR__ . '/../resource/jwt/public.key');
        $this->private_file = file_get_contents(__DIR__ . '/../resource/jwt/private.key');

        Authenticate::register(self::ISS, self::AUD, 'public', $this->public_file);
        Authenticate::register(self::ISS, self::AUD, 'private', $this->private_file);

        $this->authenticate = new Authenticate();
    }

    public function testIsTest(): void
    {
        $request = new Request(['test' => 1]);

        $response = $this->authenticate->handle($request, $this->getClosure(), self::AUD, true);

        $this->assertNull($response);
    }

    public function testInvalidTest(): void
    {
        $this->expectException(HttpException::class);

        $request = new Request(['test' => 1]);

        /** @var Response $response */
        $response = $this->authenticate->handle($request, $this->getClosure(), self::AUD);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization header', $response->getContent());
    }

    public function testValid(): void
    {
        $jwt = JwtUtils::encode(self::ISS, self::AUD, $this->private_file, 10);
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        /** @var Response $response */
        $response = $this->authenticate->handle($request, $this->getClosure(), self::AUD);

        $this->assertNull($response);
    }

    public function testInValidISS(): void
    {
        $this->expectException(HttpException::class);
        $jwt = JwtUtils::encode('invalid', self::AUD, $this->private_file, 10);

        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        /** @var Response $response */
        $response = $this->authenticate->handle($request, $this->getClosure(), self::AUD);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization public keys', $response->getContent());
    }

    public function testInValidAUD(): void
    {
        $this->expectException(HttpException::class);

        $jwt = JwtUtils::encode(self::ISS, 'invalid', $this->private_file, 10);
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        /** @var Response $response */
        $response = $this->authenticate->handle($request, $this->getClosure(), self::AUD);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization fail', $response->getContent());
    }

    private function getClosure(): callable
    {
        return function (Request $request) { return null; };
    }
}
