<?php

namespace App\Services;

use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;

class KafkaService
{
    protected $producer;
    protected $consumer;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP_SERVERS'));

        $this->producer = new Producer($conf);

        $conf = new Conf();
        $conf->set('bootstrap.servers', env('KAFKA_BOOTSTRAP_SERVERS'));
        $conf->set('group.id', env('KAFKA_GROUP_ID'));

        $this->consumer = new KafkaConsumer($conf);
        $this->consumer->subscribe(['my-topic']);
    }

    public function produce($message)
    {
        $topic = $this->producer->newTopic('my-topic');

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->flush(1000);
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
