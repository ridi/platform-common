<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartUploadException;
use Aws\ResultInterface;
use Aws\S3\MultipartUploader;
use Aws\S3\ObjectUploader;
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
     * @depecated
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
     * @param string[] $paths
     *
     * @return \Aws\Result[]
     * @throws MsgException
     */
    public function headObjectBatch(array $paths, int $concurrency = 10): array
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
            if (empty($commands)) {
                return $results;
            }

            $results = CommandPool::batch($this->client, $commands, [
                'concurrency' => $concurrency
            ]);

            foreach($results as $result) {
                if ($result instanceof AwsException) {
                    throw new MsgException($result->getMessage());
                }
            }

            return $results;
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

    public function copyObject(string $src, string $dest): bool
    {
        try {
            $src_uri = $this->parseUri($src);
            $dest_uri = $this->parseUri($dest);

            if ($src_uri['bucket'] === $dest_uri['bucket'] && $src_uri['key'] === $dest_uri['key']) {
                return true;
            }
            $params = [
                'Bucket' => $dest_uri['bucket'],
                'Key' => $dest_uri['key'],
                'CopySource' => $src_uri['bucket'] . '/' . $src_uri['key'],
            ];

            $this->client->copyObject($params);
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }

        return true;
    }

    /**
     * @depecated
     * @param string[] $src_locations
     * @param string[] $dest_locations
     *
     * @return array [ResultInterface[], AwsException[]]
     * @throws MsgException
     */
    public function copyObjects(array $src_locations, array $dest_locations, int $concurrency = 10): array
    {
        $src_count = count($src_locations);
        $dest_count = count($dest_locations);

        if ($src_count !== $dest_count) {
            throw new MsgException("different count between src_locations and dest_locations");
        }
        try {
            $commands = [];
            for ($i = 0; $i < $src_count; ++$i) {
                $src = $src_locations[$i];
                $dest = $dest_locations[$i];
                $src_uri = $this->parseUri($src);
                $dest_uri = $this->parseUri($dest);

                if ($src_uri['bucket'] === $dest_uri['bucket'] && $src_uri['key'] === $dest_uri['key']) {
                    continue;
                }

                $commands[] = $this->client->getCommand('CopyObject', [
                    'Bucket' => $dest_uri['bucket'],
                    'Key' => $dest_uri['key'],
                    'CopySource' => $src_uri['bucket'] . '/' . $src_uri['key'],
                ]);
            }

            $results = [];
            $errors = [];
            if (empty($commands)) {
                return [$results, $errors];
            }

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
     * @param string[] $src_locations
     * @param string[] $dest_locations
     *
     * @return \AWS\Result[]
     * @throws MsgException
     */
    public function copyObjectBatch(array $src_locations, array $dest_locations, int $concurrency = 10): array
    {
        $src_count = count($src_locations);
        $dest_count = count($dest_locations);

        if ($src_count !== $dest_count) {
            throw new MsgException("different count between src_locations and dest_locations");
        }
        try {
            $commands = [];
            for ($i = 0; $i < $src_count; ++$i) {
                $src = $src_locations[$i];
                $dest = $dest_locations[$i];
                $src_uri = $this->parseUri($src);
                $dest_uri = $this->parseUri($dest);

                if ($src_uri['bucket'] === $dest_uri['bucket'] && $src_uri['key'] === $dest_uri['key']) {
                    continue;
                }

                $commands[] = $this->client->getCommand('CopyObject', [
                    'Bucket' => $dest_uri['bucket'],
                    'Key' => $dest_uri['key'],
                    'CopySource' => $src_uri['bucket'] . '/' . $src_uri['key'],
                ]);
            }

            $results = [];
            if (empty($commands)) {
                return $results;
            }

            $results = CommandPool::batch($this->client, $commands, [
                'concurrency' => $concurrency
            ]);

            foreach($results as $result) {
                if ($result instanceof AwsException) {
                    throw new MsgException($result->getMessage());
                }
            }

            return $results;
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }
    }

    public function saveAsObject(string $s3_src_path, string $local_dest_path): \Aws\Result
    {
        try {
            $uri = $this->parseUri($s3_src_path);
            $params = [
                'Bucket' => $uri['bucket'],
                'Key' => $uri['key'],
                'SaveAs' => $local_dest_path
            ];

            return $this->client->getObject($params);
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @depecated
     * @param string[] $s3_src_paths
     * @param string[] $local_dest_paths
     *
     * @return array [ResultInterface[], AwsException[]]
     * @throws MsgException
     */
    public function saveAsObjects(array $s3_src_paths, array $local_dest_paths, int $concurrency = 10): array
    {
        $src_count = count($s3_src_paths);
        $dest_count = count($local_dest_paths);

        if ($src_count !== $dest_count) {
            throw new MsgException("different count between s3_src_paths and local_dest_paths");
        }
        try {
            $commands = [];
            for ($i = 0; $i < $src_count; ++$i) {
                $src_uri = $this->parseUri($s3_src_paths[$i]);
                $local_dest_path = $local_dest_paths[$i];

                $commands[] = $this->client->getCommand('GetObject', [
                    'Bucket' => $src_uri['bucket'],
                    'Key' => $src_uri['key'],
                    'SaveAs' => $local_dest_path
                ]);
            }

            $results = [];
            $errors = [];
            if (empty($commands)) {
                return [$results, $errors];
            }

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
     * @param string[] $s3_src_paths
     * @param string[] $local_dest_paths
     *
     * @return \AWS\Result[]
     * @throws MsgException
     */
    public function saveAsObjectBatch(array $s3_src_paths, array $local_dest_paths, int $concurrency = 10): array
    {
        $src_count = count($s3_src_paths);
        $dest_count = count($local_dest_paths);

        if ($src_count !== $dest_count) {
            throw new MsgException("different count between s3_src_paths and local_dest_paths");
        }
        try {
            $commands = [];
            for ($i = 0; $i < $src_count; ++$i) {
                $src_uri = $this->parseUri($s3_src_paths[$i]);
                $local_dest_path = $local_dest_paths[$i];

                $commands[] = $this->client->getCommand('GetObject', [
                    'Bucket' => $src_uri['bucket'],
                    'Key' => $src_uri['key'],
                    'SaveAs' => $local_dest_path
                ]);
            }

            $results = [];
            if (empty($commands)) {
                return $results;
            }

            $results = CommandPool::batch($this->client, $commands, [
                'concurrency' => $concurrency
            ]);

            foreach($results as $result) {
                if ($result instanceof AwsException) {
                    throw new MsgException($result->getMessage());
                }
            }

            return $results;
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-multipart-upload.html
     * @param string $local_dest_path
     * @param string $s3_src_path
     *
     * @return \Aws\Result
     * @throws MsgException
     */
    public function uploadObject(string $local_dest_path, string $s3_src_path): \Aws\Result
    {
        $source_stream = false;
        try {
            $uri = $this->parseUri($s3_src_path);
            $source_stream = fopen($local_dest_path, 'rb');
            $uploader = new ObjectUploader($this->client, $uri['bucket'], $uri['key'], $source_stream);
            do {
                try {
                    $result = $uploader->upload();
                } catch (MultipartUploadException $e) {
                    rewind($source_stream);
                    $uploader = new MultipartUploader($this->client, $source_stream, [
                        'state' => $e->getState(),
                    ]);
                }
            } while (!isset($result));
        } catch (AwsException $se) {
            throw new MsgException($se->getMessage());
        } catch (\Exception $e) {
            throw new MsgException($e->getMessage());
        } finally {
            if ($source_stream !== false) {
                fclose($source_stream);
            }
        }

        return $result;
    }
}
