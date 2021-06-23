<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Kafka\Dto;

class KafkaMessageDto
{
    /** @var string  */
    public $topic_name;
    /** @var string|null  */
    public $key;
    /** @var string|null  */
    public $value;
    /** @var string[]|null  */
    public $headers;

    /**
     * @param string $topic_name
     * @param string|null $value
     * @param string|null $key
     * @param string[]|null $headers
     * @return self
     */
    public static function import(string $topic_name, ?string $value, ?string $key, ?array $headers): self
    {
        $dto = new self();
        $dto->topic_name = $topic_name;
        $dto->value = $value;
        $dto->key = $key;
        $dto->headers = $headers;

        return $dto;
    }
}
