<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\AwsClient;
use Aws\Exception\AwsException;
use Ridibooks\Platform\Common\AWS\Dto\AwsConfigDto;
use Ridibooks\Platform\Common\Exception\MsgException;

abstract class AbstractAwsService
{
    protected $client;

    /** @var AwsClient[] $client_pool */
    private static $client_pool = [];

    abstract protected function getAwsClass(): string;

    /**
     * @param string $client_name
     * @param AwsConfigDto|null $aws_config_dto
     *
     * @return AbstractAwsService
     * @throws MsgException
     */
    public static function connect(string $client_name, ?AwsConfigDto $aws_config_dto = null)
    {
        try {
            $called_class = get_called_class();
            /** @var AbstractAwsService $class */
            $class = new $called_class();
            $class->client = $class->getClient($client_name, $aws_config_dto);

            return $class;
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @param string $client_name
     * @param AwsConfigDto|null $aws_config_dto
     *
     * @return AwsClient|null
     */
    protected function getClient(string $client_name, ?AwsConfigDto $aws_config_dto): ?AwsClient
    {
        if (!isset(self::$client_pool[$client_name])) {
            if ($aws_config_dto === null) {
                return null;
            }

            $client_class = $this->getAwsClass();
            self::$client_pool[$client_name] = new $client_class($aws_config_dto->exportToConnect());
        }

        return self::$client_pool[$client_name];
    }
}
