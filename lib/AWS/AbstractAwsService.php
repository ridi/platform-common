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

    /**
     * AbstractAwsUtils constructor.
     * @param AwsClient|null $client
     * @throws MsgException
     */
    private function __construct(?AwsClient $client)
    {
        $this->client = $client;
    }

    abstract protected static function getAwsClass();

    /**
     * @param AwsConfigDto|null $aws_config_dto
     *
     * @return AbstractAwsService
     * @throws MsgException
     */
    public static function connect(?AwsConfigDto $aws_config_dto)
    {
        $client = self::getClient($aws_config_dto);
        try {
            return new static($client);
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @param AwsConfigDto|null $aws_config_dto
     *
     * @return AwsClient|null
     */
    private static function getClient(?AwsConfigDto $aws_config_dto): ?AwsClient
    {
        $client_class = self::getAwsClass();

        if (!isset(self::$client_pool[$client_class])) {
            if ($aws_config_dto === null) {
                return null;
            }

            self::$client_pool[$client_class] = new $client_class($aws_config_dto->exportToConnect());
        }

        return self::$client_pool[$client_class];
    }
}
