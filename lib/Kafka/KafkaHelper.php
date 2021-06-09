<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Kafka;

use Exception;
use RdKafka\Conf;
use RdKafka\Producer;

class KafkaHelper
{
    private static $instance = null;
    private $producer;

    private const KAFKA_TIMEOUT = 1000;

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
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('debug', 'all');
        $conf->set('bootstrap.servers', $kafka_bootstrap_servers);
        $conf->set('enable.idempotence', 'true');

        $this->producer = new Producer($conf);
    }

    /**
     * @param string $topic_name
     * @param array $messages
     * @throws Exception
     */
    public function produce(string $topic_name, array $messages): void
    {
        $topic = $this->producer->newTopic($topic_name);

        foreach ($messages as $message) {
            $topic->produce(0, 0, json_encode($message));
            $this->producer->poll(0);
        }

        $result = $this->producer->flush(self::KAFKA_TIMEOUT);
        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new Exception(\rd_kafka_err2str($result));
        }
    }
}
