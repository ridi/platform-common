<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Credentials\CredentialProvider;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use Ridibooks\Platform\Common\AWS\Dto\AwsConfigDto;

class S3Service extends AbstractAwsService
{
    protected function getAwsClass()
    {
        return S3Client::class;
    }

    public static function registerStreamWrapperUsingAccessKey(
        string $access_key,
        string $secret_access_key,
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): void {
        $aws_config_dto = AwsConfigDto::importFromAccessKeys($access_key, $secret_access_key, $region, $version);

        self::registerStreamWrapper($aws_config_dto);
    }

    public static function registerStreamWrapperUsingDefaultCredential(
        string $region = 'ap-northeast-2',
        string $version = 'latest'
    ): void {
        $credentials = CredentialProvider::defaultProvider();
        $aws_config_dto = AwsConfigDto::importFromCredentials($credentials, $region, $version);
        self::registerStreamWrapper($aws_config_dto);
    }

    private static function registerStreamWrapper(AwsConfigDto $aws_config_dto): void
    {
        $client = new S3Client($aws_config_dto->exportToConnect());
        $client->registerStreamWrapper();

        // add global ACL options
        $default = stream_context_get_options(stream_context_get_default());
        $default['s3']['ACL'] = $default['s3']['acl'] = 'private';
        stream_context_set_default($default);
    }

    public function transferFile(string $s3_path, string $dest_path): bool
    {
        if (!is_dir($dest_path)) {
            @mkdir($dest_path, 0777, true);
        }

        try {
            $manager = new Transfer($this->client, $s3_path, $dest_path);
            $manager->transfer();
        } catch (S3Exception $se) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
