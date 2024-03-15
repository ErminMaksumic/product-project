<?php

namespace App\Kafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;

class Consumer
{
    private $consumer;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP_SERVERS'));
        $conf->set('group.id', env('KAFKA_GROUP_ID'));

        $this->consumer = new KafkaConsumer($conf);
        $this->consumer->subscribe(['my-topic']);
    }

    public function consume()
    {
        while (true) {
            $message = $this->consumer->consume(120 * 1000);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    // Process the consumed message
                    echo $message->payload . PHP_EOL;
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    // End of partition, no more messages
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // No message within the given timeout
                    break;
                default:
                    // Handle other errors
                    echo $message->errstr() . PHP_EOL;
                    break;
            }
        }
    }
}