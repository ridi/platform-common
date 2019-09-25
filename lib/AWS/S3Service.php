<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\S3\S3UriParser;
use Aws\S3\Transfer;
use Ridibooks\Platform\Common\Util\FileUtils;

/**
* @property S3Client $client
*/
class S3Service extends AbstractAwsService
{
    private $s3_uri_parser;

    private function __construct()
    {
        $this->s3_uri_parser = new S3UriParser();
    }

    protected function getAwsClass(): string
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

    public function transferFile(string $src_path, string $dest_path): bool
    {
        if (!FileUtils::isS3Scheme($dest_path) && !is_dir($dest_path)) {
            @mkdir($dest_path, 0777, true);
        }

        try {
            $manager = new Transfer($this->client, $src_path, $dest_path);
            $manager->transfer();
        } catch (S3Exception $se) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * bucket: The Amazon S3 bucket (null if none)
     * key: The Amazon S3 key (null if none)
     * path_style: Set to true if using path style, or false if not
     * region: Set to a string if a non-class endpoint is used or null.
     *
     * @param string $src
     *
     * @return array
     */
    public function parseUri(string $src): array
    {
        return $this->s3_uri_parser->parse($src);
    }

    public function headObject(string $src)
    {
        $uri = $this->parseUri($src);

        $params = [
            'Bucket' => $uri['bucket'],
            'Key' => $uri['key'],
        ];

        return $this->client->headObject($params);
    }
}
