<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public function publish($message)
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->exchange_declare('laravel_exchange', 'direct', false, false, false);
        $channel->queue_declare('laravel_queue', false, false, false, false);
        $channel->queue_bind('laravel_queue', 'laravel_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'laravel_exchange', 'test_key');
        echo "Sent $message to laravel_exchange / laravel_queue.\n";
        $channel->close();
        $connection->close();
    }

    public function consume()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $channel->queue_declare('laravel_queue', false, false, false, false);

        $messages = [];

        while (true) {
            $message = $channel->basic_get('laravel_queue');


            if ($message !== null) {
                $messages[] = json_decode($message->body, true);
                //If u want to delete msg after its acknowledged
                //$channel->basic_ack($message->delivery_info['delivery_tag']);
            } else {
                break;
            }
        }

        $channel->close();
        $connection->close();

        return $messages;
    }
}
