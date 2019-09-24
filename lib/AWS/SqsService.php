<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Ridibooks\Platform\Common\AWS\Dto\AwsConfigDto;
use Ridibooks\Platform\Common\Exception\MsgException;

/**
 * @property SqsClient $client
 */
class SqsService extends AbstractAwsService
{
    /** @var string */
    private $queue_url;

    /**
     * SQSUtils constructor.
     * @param string $queue_url
     * @param AwsConfigDto $aws_config
     * @throws MsgException
     */
    public function __construct(AwsConfigDto $aws_config)
    {
        parent::__construct($aws_config);
        $this->queue_url = $aws_config->params['queue_url'];
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
