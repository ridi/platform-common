<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthorizationFactory
{
    /** @var JwtAuthDto[] */
    private static $auth_dtos = [];

    public static function getAuthorizationUtil(string $aud, bool $is_debug = false): callable
    {
        return function (Request $request) use ($aud, $is_debug): ?Response {
            if ($is_debug && $request->query->get('test')) {
                return null;
            }

            $authorization = $request->headers->get('Authorization');
            $has_bearer_authorization = strpos($authorization, 'Bearer ') === 0;
            if (empty($authorization) || !$has_bearer_authorization) {
                return new Response('invalid authorization header', Response::HTTP_UNAUTHORIZED);
            }

            $authorization = substr($authorization, 7);
            $payload = explode('.', $authorization)[1];
            if (empty($payload)) {
                return new Response('invalid authorization header', Response::HTTP_UNAUTHORIZED);
            }

            $payload = json_decode(base64_decode($payload), true);
            if (empty($payload['iss'])) {
                return new Response('invalid authorization payload', Response::HTTP_UNAUTHORIZED);
            }

            $payload_iss = str_replace(['-', '_'], '', $payload['iss']);
            $payload_aud = str_replace(['-', '_'], '', $aud);

            /** @var JwtAuthDto[] $auth_dtos */
            $auth_dtos = self::$auth_dtos[$payload_iss][$payload_aud]['public'];
            if (empty($auth_dtos)) {
                return new Response('invalid authorization public keys', Response::HTTP_UNAUTHORIZED);
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
                return new Response('invalid authorization fail', Response::HTTP_UNAUTHORIZED);
            }

            return null;
        };
    }

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
}
