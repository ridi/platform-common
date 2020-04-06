<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Ridibooks\Platform\Common\Dto\AwsConfigDto;
use Ridibooks\Platform\Common\Exception\MsgException;

/**
 * @property SqsClient $client
 * @deprecated
 */
class SQSUtils extends AbstractAwsUtils
{
    /** @var string */
    private $queue_url;

    /**
     * SQSUtils constructor.
     * @param string $queue_url
     * @param AwsConfigDto $aws_config
     * @throws MsgException
     */
    public function __construct(string $queue_url, AwsConfigDto $aws_config)
    {
        parent::__construct($aws_config);
        trigger_error('Deprecated SQSUtils - Use AWS\SqsService');

        $this->queue_url = $queue_url;
    }

    protected function getAwsClass(): string
    {
        return SqsClient::class;
    }

    /**
     * @param array $attributes
     * @param string $message
     * @param int $delay_seconds
     * @throws MsgException
     */
    public function addMessage(array $attributes, string $message, int $delay_seconds = 10): void
    {
        $params = [
            'DelaySeconds' => $delay_seconds,
            'MessageAttributes' => $attributes,
            'QueueUrl' => $this->queue_url,
            'MessageBody' => $message,
        ];

        try {
            $this->client->sendMessage($params);
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}
