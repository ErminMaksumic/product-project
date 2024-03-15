<?php

namespace App\Kafka;

use RdKafka\Conf;
use RdKafka\Producer as RdKafkaProducer;

class Producer
{
    private $producer;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP_SERVERS'));

        $this->producer = new RdKafkaProducer($conf);
    }

    public function produce($message)
    {
        $topic = $this->producer->newTopic('my-topic');

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->flush(1000);
    }
}
