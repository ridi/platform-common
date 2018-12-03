<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common;

use Aws\Exception\AwsException;
use Ridibooks\Platform\Common\Dto\AwsConfigDto;
use Ridibooks\Platform\Common\Exception\MsgException;

abstract class AbstractAwsUtils
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
        $client_type = $this->getAwsClass();

        try {
            $this->client = new $client_type($aws_config->exportToConnect());
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}
