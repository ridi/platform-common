<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS\Dto;

class AwsConfigDto
{
    /* @var string */
    public $key;
    /* @var string */
    public $secret;
    /** @var array|callable */
    public $credentials;
    /* @var string */
    public $region;
    /* @var string */
    public $version;

    public static function importFromAccessKeys(
        string $key,
        string $secret,
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): self {
        $dto = new self;
        $dto->key = $key;
        $dto->secret = $secret;
        $dto->credentials = [
            'key' => $dto->key,
            'secret' => $dto->secret,
        ];

        $dto->region = $region;
        $dto->version = $version;

        return $dto;
    }

    public static function importFromCredentials(
        $credentials,
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): self {
        $dto = new self;
        $dto->credentials = $credentials;
        $dto->region = $region;
        $dto->version = $version;

        return $dto;
    }

    public function exportToConnect(): array
    {
        return [
            'credentials' => $this->credentials,
            'region' => $this->region,
            'version' => $this->version,
        ];
    }
}
