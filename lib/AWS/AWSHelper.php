<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Credentials\CredentialProvider;
use Ridibooks\Platform\Common\AWS\Dto\AwsConfigDto;

class AWSHelper
{
    public static function getAwsConfigDto(?string $access_key, ?string $secret_access_key): AwsConfigDto
    {
        $credential = CredentialProvider::defaultProvider();
        $aws_config_dto = AwsConfigDto::importFromCredentials($credential);

        if (!empty($access_key) && !empty($secret_access_key)) {
            $aws_config_dto = AwsConfigDto::importFromAccessKeys($access_key, $secret_access_key);
        }

        return $aws_config_dto;
    }
}
