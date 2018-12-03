<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Dto;

class AwsConfigDto
{
    /* @var string */
    public $key;
    /* @var string */
    public $secret;
    /* @var string */
    public $region;
    /* @var string */
    public $version;

    public static function importFromParam(
        string $key,
        string $secret,
        string $region,
        string $version = 'latest'
    ): self {
        $dto = new self;
        $dto->key = $key;
        $dto->secret = $secret;
        $dto->region = $region;
        $dto->version = $version;

        return $dto;
    }

    public function exportToConnect(): array
    {
        return [
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ],
            'region' => $this->region,
            'version' => $this->version,
        ];
    }
}
