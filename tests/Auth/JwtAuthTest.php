<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Auth;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Auth\JwtAuthorizationFactory;
use Ridibooks\Platform\Common\Auth\JwtUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthTest extends TestCase
{
    private const ISS = 'test';
    private const AUD = 'test';

    private $public_file;
    private $private_file;

    public function setUp(): void
    {
        $this->public_file = file_get_contents(__DIR__ . '/../resource/jwt/public.key');
        $this->private_file = file_get_contents(__DIR__ . '/../resource/jwt/private.key');

        JwtAuthorizationFactory::register(self::ISS, self::AUD, 'public', $this->public_file);
        JwtAuthorizationFactory::register(self::ISS, self::AUD, 'private', $this->private_file);
    }

    public function testIsTest(): void
    {
        $request = new Request(['test' => 1]);

        $callable = JwtAuthorizationFactory::getAuthorizationUtil(self::AUD, true);
        $response = $callable($request);

        $this->assertNull($response);
    }

    public function testInvalidTest(): void
    {
        $request = new Request(['test' => 1]);

        $callable = JwtAuthorizationFactory::getAuthorizationUtil(self::AUD);
        /** @var Response $response */
        $response = $callable($request);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization header', $response->getContent());
    }

    public function testValid(): void
    {
        $jwt = JwtUtils::encode(self::ISS, self::AUD, $this->private_file, 10);
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        $callable = JwtAuthorizationFactory::getAuthorizationUtil(self::AUD);
        /** @var Response $response */
        $response = $callable($request);

        $this->assertNull($response);
    }

    public function testInValidISS(): void
    {
        $jwt = JwtUtils::encode('invalid', self::AUD, $this->private_file, 10);
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        $callable = JwtAuthorizationFactory::getAuthorizationUtil(self::AUD);
        /** @var Response $response */
        $response = $callable($request);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization public keys', $response->getContent());
    }

    public function testInValidAUD(): void
    {
        $jwt = JwtUtils::encode(self::ISS, 'invalid', $this->private_file, 10);
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $jwt]);

        $callable = JwtAuthorizationFactory::getAuthorizationUtil(self::AUD);
        /** @var Response $response */
        $response = $callable($request);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
        $this->assertStringContainsString('invalid authorization fail', $response->getContent());
    }
}
