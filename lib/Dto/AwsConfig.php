<?php
namespace Ridibooks\Platform\Common\Dto;

class AwsConfig
{
    /* @var string */
    public $key;
    /* @var string */
    public $secret;
    /* @var string */
    public $region;
    /* @var string */
    public $version;

    public static function importFromParam(string $key, string $secret, string $region, string $version)
    {
        $dto = new self;
        $dto->key = $key;
        $dto->secret = $secret;
        $dto->region = $region;
        $dto->version = $version;

        return $dto;
    }
}
