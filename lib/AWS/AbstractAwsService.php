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

    /**
     * AbstractAwsUtils constructor.
     * @param AwsConfigDto $aws_config
     * @throws MsgException
     */
    public function __construct(AwsConfigDto $aws_config)
    {
        $aws_class = $this->getAwsClass();

        $this->client = new $aws_class($aws_config->exportToConnect());
    }

    abstract protected function getAwsClass();

    /**
     * @param AwsConfigDto $aws_config
     *
     * @return static
     * @throws MsgException
     */
    public static function connect(AwsConfigDto $aws_config)
    {
        try {
            return new static($aws_config);
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}
