<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\ResultInterface;
use Aws\S3\S3Client;
use Aws\S3\S3UriParser;
use Aws\S3\Transfer;
use GuzzleHttp\Promise\PromiseInterface;
use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\FileUtils;

/**
 * @property S3Client $client
 */
class S3Service extends AbstractAwsService
{
    private $s3_uri_parser;

    protected function __construct()
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
        } catch (AwsException $se) {
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

    /**
     * @param string $src
     *
     * @return \Aws\Result
     * @throws MsgException
     */
    public function headObject(string $src)
    {
        try {
            $uri = $this->parseUri($src);

            $params = [
                'Bucket' => $uri['bucket'],
                'Key' => $uri['key'],
            ];

            return $this->client->headObject($params);
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @param string[] $paths
     *
     * @return array [ResultInterface[], AwsException[]]
     * @throws MsgException
     */
    public function headObjects(array $paths, int $concurrency = 10): array
    {
        try {
            $commands = [];
            foreach ($paths as $path) {
                $uri = $this->parseUri($path);
                $commands[] = $this->client->getCommand('HeadObject', [
                    'Bucket' => $uri['bucket'],
                    'Key' => $uri['key'],
                ]);
            }

            $results = [];
            $errors = [];
            $pool = new CommandPool($this->client, $commands, [
                'concurrency' => $concurrency,
                'fulfilled' => function (ResultInterface $result, $iterKey, PromiseInterface $promise) use (&$results) {
                    $results[] = $result;
                },
                'rejected' => function (AwsException $reason, $iterKey, PromiseInterface $promise) use (&$errors) {
                    $errors[] = $reason;
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();

            return [$results, $errors];
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @param string               $src
     * @param int|string|\DateTime $expires The time at which the URL should
     *                                      expire. This can be a Unix timestamp, a PHP DateTime object, or a
     *                                      string that can be evaluated by strtotime.
     * @param string|null          $original_filename
     *
     * @return string|null
     * @throws MsgException
     */
    public function createPresignedUrl(string $src, $expires, string $original_filename = null): ?string
    {
        if (!$this->doesObjectExist($src)) {
            return null;
        }
        $uri = $this->parseUri($src);

        $params = [
            'Bucket' => $uri['bucket'],
            'Key' => $uri['key'],
        ];

        if ($original_filename !== null) {
            // https://stackoverflow.com/questions/3856362/php-rfc-2231-how-to-encode-utf-8-string-as-content-disposition-filename
            $params['ResponseContentDisposition'] = 'attachment; filename*=UTF-8\'\'' . rawurlencode($original_filename);
        }

        try {
            $cmd = $this->client->getCommand('GetObject', $params);
            $request = $this->client->createPresignedRequest($cmd, $expires);
        } catch (\Throwable $e) {
            trigger_error($e->getMessage());

            return null;
        }

        return (string)$request->getUri();
    }

    /**
     * @param string $src
     *
     * @return bool
     * @throws MsgException
     */
    public function doesObjectExist(string $src): bool
    {
        try {
            $uri = $this->parseUri($src);

            return $this->client->doesObjectExist($uri['bucket'], $uri['key']);
        } catch (\Throwable $e) {
            trigger_error($e->getMessage());

            return false;
        }
    }
}
