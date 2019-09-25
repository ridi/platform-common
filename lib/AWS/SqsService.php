<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Ridibooks\Platform\Common\Exception\MsgException;

/**
 * @property SqsClient $client
 */
class SqsService extends AbstractAwsService
{
    /** @var string */
    public $queue_url = '';

    protected function getAwsClass(): string
    {
        return SqsClient::class;
    }

    public function setQueueUrl(string $queue_url): void
    {
        $this->queue_url = $queue_url;
    }

    /**
     * @param array  $attributes
     * @param string $message
     * @param int    $delay_seconds
     *
     * @throws MsgException
     */
    public function addMessage(array $attributes, string $message, int $delay_seconds = 10): void
    {
        if (empty($this->queue_url)) {
            throw new MsgException('empty queue url');
        }

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
