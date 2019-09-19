<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class S3Utils
{
    public static function registerStreamWrapperUsingAccessKey(
        string $access_key,
        string $secret_access_key,
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): void {
        $credential = [
            'key' => $access_key,
            'secret' => $secret_access_key,
        ];

        self::registerStreamWrapper($credential, $region, $version);
    }

    public static function registerStreamWrapperUsingDefaultCredential(
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): void {
        $credential = CredentialProvider::defaultProvider();
        self::registerStreamWrapper($credential, $region, $version);
    }

    private static function registerStreamWrapper($credential, string $region, string $version): void
    {
        $config = [
            'region' => $region,
            'version' => $version,
            'credential' => $credential,
        ];

        $client = new S3Client($config);
        $client->registerStreamWrapper();

        // add global ACL options
        $default = stream_context_get_options(stream_context_get_default());
        $default['s3']['ACL'] = $default['s3']['acl'] = 'private';
        stream_context_set_default($default);
    }
}
