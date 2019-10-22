<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Auth;

class JwtAuthDto
{
    /** @var string required */
    public $iss;
    /** @var string required */
    public $aud;
    /** @var string required enum - {public, private} */
    public $key_type;
    /** @var string required */
    public $key;
    /** @var \DateTime|null optional */
    public $not_valid_before;
    /** @var \DateTime|null optional */
    public $not_valid_after;

    public static function import(
        string $iss,
        string $aud,
        string $key_type,
        string $key,
        ?\DateTime $not_valid_before = null,
        ?\DateTime $not_valid_after = null
    ): self {
        $dto = new self();
        $dto->iss = $iss;
        $dto->aud = $aud;
        $dto->key_type = $key_type;
        $dto->key = $key;
        $dto->not_valid_before = $not_valid_before;
        $dto->not_valid_after = $not_valid_after;

        return $dto;
    }
}
