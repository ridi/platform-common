<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Credentials\CredentialProvider;
use Aws\DoctrineCacheAdapter;
use Doctrine\Common\Cache\FilesystemCache;
use Ridibooks\Platform\Common\AWS\Dto\AwsConfigDto;

class AWSHelper
{
    public static function getAwsDefaultConfigDto(bool $is_cached = false): AwsConfigDto
    {
        $credential = CredentialProvider::defaultProvider();

        if ($is_cached) {
            $credential = CredentialProvider::cache(
                $credential,
                new DoctrineCacheAdapter(new FilesystemCache('/tmp/cache'))
            );
        }

        return AwsConfigDto::importFromCredentials($credential);
    }

    public static function getAwsConfigDto(?string $access_key, ?string $secret_access_key, bool $is_cached = false): AwsConfigDto
    {
        $aws_config_dto = self::getAwsDefaultConfigDto($is_cached);

        if (!empty($access_key) && !empty($secret_access_key)) {
            $aws_config_dto = AwsConfigDto::importFromAccessKeys($access_key, $secret_access_key);
        }

        return $aws_config_dto;
    }
}
