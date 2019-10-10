<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS\Dto;

class SqsReceivedMessageDto
{
    /** @var string[] */
    public $attributes = [];
    /** @var string */
    public $receipt_handle;
    /** @var array */
    public $message_attributes = [];
    /** @var array */
    public $body = [];

    public static function importFromReceivedItem(array $item): self
    {
        $dto = new self;
        $dto->attributes = $item['Attributes'];
        $dto->receipt_handle = $item['ReceiptHandle'];
        $dto->message_attributes = $item['MessageAttributes'];
        $dto->body = json_decode($item['Body'], true);

        return $dto;
    }
}
