<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Auth;

use Firebase\JWT\JWT;

class JwtUtils
{
    public const ALG_RS256 = 'RS256';

    /** @var string */
    private $public_key;
    /** @var string - using TestJewUtils */
    protected $algorithm;

    public function __construct(string $public_key, string $algorithm = self::ALG_RS256)
    {
        $this->public_key = $public_key;
        $this->algorithm = $algorithm;
    }

    public function decode(string $jwt): array
    {
        return (array)JWT::decode($jwt, $this->public_key, [$this->algorithm]);
    }

    public static function encode(
        string $iss,
        string $aud,
        string $private_key,
        int $exp,
        ?string $sub = null
    ): string {
        $payload = [
            'iss' => $iss,
            'aud' => $aud,
            'exp' => (new \DateTime())->getTimestamp() + $exp,
        ];

        if ($sub !== null) {
            $payload['sub'] = $sub;
        }

        return JWT::encode($payload, $private_key, self::ALG_RS256);
    }
}
