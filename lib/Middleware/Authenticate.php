<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ridibooks\Platform\Common\Auth\JwtAuthDto;
use Ridibooks\Platform\Common\Auth\JwtUtils;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Authenticate
 * @package Ridibooks\Platform\Common\Middleware
 */
class Authenticate
{
    /** @var JwtAuthDto[] */
    private static $auth_dtos = [];

    public static function register(
        string $iss,
        string $aud,
        string $key_type,
        string $key,
        ?\DateTime $not_valid_before = null,
        ?\DateTime $not_valid_after = null
    ): void {
        $jwt_auth_dto = JwtAuthDto::import($iss, $aud, $key_type, $key, $not_valid_before, $not_valid_after);

        if (!is_array(self::$auth_dtos[$iss][$aud][$key_type])) {
            self::$auth_dtos[$iss][$aud][$key_type] = [];
        }

        self::$auth_dtos[$iss][$aud][$key_type][] = $jwt_auth_dto;
    }

    public function handle(Request $request, \Closure $next, string $aud, bool $is_debug = false)
    {
        if ($is_debug && $request->query->get('test')) {
            return $next($request);
        }

        $authorization = $request->headers->get('Authorization');
        $has_bearer_authorization = strpos($authorization, 'Bearer ') === 0;
        if (empty($authorization) || !$has_bearer_authorization) {
            $this->abort(Response::HTTP_UNAUTHORIZED, 'invalid authorization header');
        }

        $authorization = substr($authorization, 7);
        $payload = explode('.', $authorization)[1];
        if (empty($payload)) {
            $this->abort(Response::HTTP_UNAUTHORIZED, 'invalid authorization header');
        }

        $payload = json_decode(base64_decode($payload), true);
        if (empty($payload['iss'])) {
            $this->abort(Response::HTTP_UNAUTHORIZED, 'invalid authorization payload');
        }

        $payload_iss = str_replace(['-', '_'], '', $payload['iss']);
        $payload_aud = str_replace(['-', '_'], '', $aud);

        /** @var JwtAuthDto[] $auth_dtos */
        $auth_dtos = self::$auth_dtos[$payload_iss][$payload_aud]['public'];
        if (empty($auth_dtos)) {
            $this->abort(Response::HTTP_UNAUTHORIZED, 'invalid authorization public keys');
        }

        $is_valid = false;
        $now = new \DateTime();
        foreach ($auth_dtos as $auth_dto) {
            if ($auth_dto->not_valid_before !== null && $auth_dto->not_valid_before > $now) {
                continue;
            }
            if ($auth_dto->not_valid_after !== null && $auth_dto->not_valid_after < $now) {
                continue;
            }
            $jwt_utils = new JwtUtils($auth_dto->key, JwtUtils::ALG_RS256);

            try {
                $payload = $jwt_utils->decode($authorization);
                if ($payload['aud'] === $aud) {
                    $is_valid = true;
                }
            } catch (\Exception $e) {
                // nothing
            }
        }
        if (!$is_valid) {
            $this->abort(Response::HTTP_UNAUTHORIZED, 'invalid authorization fail');
        }

        return $next($request);
    }

    /**
     * 참고) Laravel\Lumen\Concerns\RegistersExceptionHandlers
     * Throw an HttpException with the given data.
     *
     * @param int    $code
     * @param string $message
     * @param array  $headers
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function abort(int $code, string $message = '', array $headers = []): void
    {
        if ($code === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }
}
