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
     * @param string $client_name
     * @param AwsConfigDto|null $aws_config_dto
     *
     * @return AbstractAwsService
     * @throws MsgException
     */
    public static function connect(string $client_name, ?AwsConfigDto $aws_config_dto = null)
    {
        try {
            $client = self::getClient($client_name, $aws_config_dto);
            $called_class = get_called_class();

            return new $called_class($client);
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
    private static function getClient(string $client_name, ?AwsConfigDto $aws_config_dto): ?AwsClient
    {
        if (!isset(self::$client_pool[$client_name])) {
            if ($aws_config_dto === null) {
                return null;
            }

            $client_class = self::getAwsClass();
            self::$client_pool[$client_name] = new $client_class($aws_config_dto->exportToConnect());
        }

        return self::$client_pool[$client_name];
    }
}
