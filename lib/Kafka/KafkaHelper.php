<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Kafka;

use Exception;
use RdKafka\Conf;
use RdKafka\Producer;
use Ridibooks\Platform\Common\Kafka\Dto\KafkaMessageDto;

class KafkaHelper
{
    private static $instance = null;
    private $producer;

    private const MAX_FLUSH_COUNT = 5;

    public static function getConnection(string $kafka_bootstrap_servers): self
    {
        if (self::$instance === null) {
            $kafka = new KafkaHelper($kafka_bootstrap_servers);
            self::$instance = $kafka;
        }

        return self::$instance;
    }

    private function __construct(string $kafka_bootstrap_servers)
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', $kafka_bootstrap_servers);
        $conf->setDrMsgCb(function ($kafka, $message) {
            if ($message->err) {
                throw new Exception($message->errstr());
            }
        });
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            throw new Exception("Kafka error: " . rd_kafka_err2str($err) . " (reason: $reason)");
        });

        $this->producer = new Producer($conf);
    }

    /**
     * @param KafkaMessageDto $message_dto
     * @throws Exception
     */
    public function produce(KafkaMessageDto $message_dto): void
    {
        $topic = $this->producer->newTopic($message_dto->topic_name);

        $topic->producev(
            RD_KAFKA_PARTITION_UA,
            0,
            $message_dto->value,
            $message_dto->key,
            $message_dto->headers
        );
        $this->producer->poll(0);

        for ($flush_count = 1; $flush_count <= self::MAX_FLUSH_COUNT; $flush_count++) {
            $result = $this->producer->flush(1000 * $flush_count);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                return;
            }
        }

        throw new Exception('Message failed to send.');
    }
}
