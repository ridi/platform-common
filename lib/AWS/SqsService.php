<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Ridibooks\Platform\Common\AWS\Dto\SqsReceivedMessageDto;
use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\SentryHelper;

/**
 * @property SqsClient $client
 */
class SqsService extends AbstractAwsService
{
    protected function getAwsClass(): string
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
    public function sendMessage(string $queue_url, array $attributes, string $message, int $delay_seconds = 10): void
    {
        if (empty($queue_url)) {
            throw new MsgException('empty queue url');
        }

        $params = [
            'DelaySeconds' => $delay_seconds,
            'MessageAttributes' => $attributes,
            'QueueUrl' => $queue_url,
            'MessageBody' => $message,
        ];

        try {
            $this->client->sendMessage($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Receive Message From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw new MsgException($e->getMessage());
        }
    }

    /**
     * @param  string $queue_url
     * @param  int $max_number_of_messages
     * @return SqsReceivedMessageDto[]
     * @throws MsgException
     */
    public function receiveMessages(string $queue_url, int $max_number_of_messages = 1): array
    {
        if ($max_number_of_messages > 10) {
            $max_number_of_messages = 10;
        }

        $receive_param = [
            'AttributeNames' => ['SentTimestamp'],
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $queue_url,
            'WaitTimeSeconds' => 0,
            'MaxNumberOfMessages' => $max_number_of_messages
        ];

        try {
            $receive_result = $this->client->receiveMessage($receive_param);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Receive Message From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw new MsgException($e->getMessage());
        }

        $items = $receive_result->get('Messages');
        if (empty($items)) {
            return [];
        }
        unset($receive_result);

        return collect($items)
            ->map(function ($item) {
                return SqsReceivedMessageDto::importFromReceivedItem($item);
            })
            ->all();
    }

    public function deleteMessage(string $queue_url, string $receipt_handle): void
    {
        $params = [
            'QueueUrl' => $queue_url,
            'ReceiptHandle' => $receipt_handle,
        ];

        try {
            $this->client->deleteMessage($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Delete Message : ' . $receipt_handle . ' From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw new MsgException($e->getMessage());
        }
    }
}
