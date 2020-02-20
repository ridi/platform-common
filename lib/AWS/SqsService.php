<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Ridibooks\Platform\Common\AWS\Dto\SqsQueueAttributeDto;
use Ridibooks\Platform\Common\AWS\Dto\SqsReceivedMessageDto;
use Ridibooks\Platform\Common\Exception\MsgException;
use Ridibooks\Platform\Common\Util\SentryHelper;
use Ridibooks\Platform\Common\Util\StringUtils;

/**
 * @property SqsClient $client
 */
class SqsService extends AbstractAwsService
{
    /** @var SqsQueueAttributeDto[] */
    private static $queue_attributes = [];

    protected function getAwsClass(): string
    {
        return SqsClient::class;
    }

    public function getQueueAttributes(string $queue_url): ?SqsQueueAttributeDto
    {
        $parsed_queue_url = parse_url($queue_url);

        try {
            $queue_name = array_pop(explode('/', $parsed_queue_url['path']));
            if (!isset(self::$queue_attributes[$queue_name])) {
                $params = ['AttributeNames' => ['All'], 'QueueUrl' => $queue_url];
                $result = $this->client->getQueueAttributes($params);
                $result = $result->get('Attributes');
                self::$queue_attributes[$queue_name] = SqsQueueAttributeDto::import($queue_url, $result);
            }

            return self::$queue_attributes[$queue_name];
        } catch (AwsException $e) {
            return null;
        }
    }
    /**
     * @param string $queue_url
     * @param array  $attributes
     * @param string $message
     *
     * @throws MsgException
     */
    public function sendMessage(string $queue_url, array $attributes, string $message): void
    {
        if (StringUtils::isEmpty($queue_url)) {
            throw new MsgException('invalid queue url');
        }

        $params = [
            'MessageAttributes' => $attributes,
            'QueueUrl' => $queue_url,
            'MessageBody' => $message,
        ];

        try {
            $this->client->sendMessage($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Send Message From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw $e;
        }
    }

    public function sendMessageToFifoQueue(
        string $queue_url,
        string $group_id,
        string $deduplication_id,
        array $attributes,
        string $message
    ): void {
        if (empty($queue_url)) {
            throw new MsgException('empty queue url');
        }

        $params = [
            'MessageGroupId' => $group_id,
            'MessageDeduplicationId' => $deduplication_id,
            'MessageAttributes' => $attributes,
            'QueueUrl' => $queue_url,
            'MessageBody' => $message,
        ];

        try {
            $this->client->sendMessage($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Send Message From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw $e;
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
            throw $e;
        }

        $items = $receive_result->get('Messages');
        if (empty($items)) {
            return [];
        }
        unset($receive_result);

        $message_dtos = [];
        foreach ($items as $item) {
            $message_dtos[] = SqsReceivedMessageDto::importFromReceivedItem($item);
        }

        return $message_dtos;
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
            throw $e;
        }
    }

    public function changeMessageVisibility(string $queue_url, string $receipt_handle, int $visibility_timeout): void
    {
        $params = [
            'QueueUrl' => $queue_url,
            'ReceiptHandle' => $receipt_handle,
            'VisibilityTimeout' => $visibility_timeout,
        ];

        try {
            $this->client->changeMessageVisibility($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To Change Visibility: ' . $receipt_handle . ' From :' . $queue_url . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw $e;
        }
    }
}
