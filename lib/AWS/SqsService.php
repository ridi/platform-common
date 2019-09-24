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
    protected static function getAwsClass(): string
    {
        return SqsClient::class;
    }

    /**
     * @param string $queue_url
     * @param array  $attributes
     * @param string $message
     * @param int    $delay_seconds
     *
     * @throws MsgException
     */
    public function addMessage(string $queue_url, array $attributes, string $message, int $delay_seconds = 10): void
    {
        $params = [
            'DelaySeconds' => $delay_seconds,
            'MessageAttributes' => $attributes,
            'QueueUrl' => $queue_url,
            'MessageBody' => $message,
        ];

        try {
            $this->client->sendMessage($params);
        } catch (AwsException $e) {
            throw new MsgException($e->getMessage());
        }
    }
}
