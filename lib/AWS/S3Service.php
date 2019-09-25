<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3\Transfer;

/**
* @property S3Client $client
*/
class S3Service extends AbstractAwsService
{
    protected static function getAwsClass()
    {
        return S3Client::class;
    }

    public function registerStreamWrapper(): void
    {
        $this->activateStreamWrapper();
    }

    private function activateStreamWrapper(): void
    {
        $this->client->registerStreamWrapper();

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
