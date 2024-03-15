<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kafka\Producer;
use App\Kafka\Consumer;

class KafkaController extends Controller
{
    protected $producer;
    protected $consumer;

    public function __construct(Producer $producer, Consumer $consumer)
    {
        $this->producer = $producer;
        $this->consumer = $consumer;
    }

    /**
     * Produce a message to Kafka.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function produce(Request $request)
    {
        $message = $request->input('message');
        $this->producer->produce($message);

        return response()->json(['success' => true, 'message' => 'Message produced to Kafka', 'resp' => $message]);
    }

    /**
     * Consume a message from Kafka.
     *
     * @return \Illuminate\Http\Response
     */
    public function consume()
    {
        $message = $this->consumer->consume();

        return response()->json(['success' => true, 'message' => $message]);
    }
}
