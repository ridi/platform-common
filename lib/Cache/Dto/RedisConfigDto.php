<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Cache\Dto;

class RedisConfigDto
{
    /** @var array */
    public $hosts = [];
    /** @var array */
    public $options = [];

    public static function import(array $hosts, array $options): self
    {
        $dto = new self();
        $dto->hosts = $hosts;
        $dto->options = $options;

        return $dto;
    }
}
