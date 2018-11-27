<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common;

use Aws\Exception\AwsException;
use Ridibooks\Platform\Common\Dto\AwsConfig;
use Ridibooks\Platform\Common\Exception\MsgException;

abstract class AbstractAwsUtils
{
    protected $client;

    /**
     * AbstractAwsUtils constructor.
     * @param AwsConfig $aws_config
     * @throws MsgException
     */
    public function __construct(AwsConfig $aws_config)
    {
        $this->connect($aws_config);
    }

    abstract public static function getClient();

    /**
     * @param AwsConfig $aws_config
     * @throws MsgException
     */
    protected function connect(AwsConfig $aws_config)
    {
        $client_type = self::getClient();

        try {
            $this->client = new $client_type($aws_config);
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}

