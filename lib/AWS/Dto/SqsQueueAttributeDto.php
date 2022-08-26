<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS\Dto;

class SqsQueueAttributeDto
{
    /** @var string */
    public $queue_url;
    /** @var int */
    public $visibility_timeout = 0;
    /** @var int */
    public $delay_seconds = 0;
    /** @var int */
    public $receive_message_wait_time_seconds = 0;
    /** @var int */
    public $message_retention_period = 15;

    public static function import(string $queue_url, array $dict): self
    {
        $dto = new self();
        $dto->queue_url = $queue_url;
        $dto->visibility_timeout = $dict['VisibilityTimeout'];
        $dto->delay_seconds = $dict['DelaySeconds'];
        $dto->receive_message_wait_time_seconds = $dict['ReceiveMessageWaitTimeSeconds'];
        $dto->message_retention_period = $dict['MessageRetentionPeriod'];

        return $dto;
    }
}
