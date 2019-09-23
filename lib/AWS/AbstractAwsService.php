<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

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
        $this->connect($aws_config);
    }

    abstract protected function getAwsClass();

    /**
     * @param AwsConfigDto $aws_config
     * @throws MsgException
     */
    protected function connect(AwsConfigDto $aws_config)
    {
        $aws_class = $this->getAwsClass();

        try {
            $this->client = new $aws_class($aws_config->exportToConnect());
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}
