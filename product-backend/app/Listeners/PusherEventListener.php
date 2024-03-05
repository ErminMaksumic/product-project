<?php

namespace App\Listeners;

use App\Events\PusherEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PusherEventListener
{
    public function __construct()
    {
        //
    }

    public function handle(PusherEvent $event)
    {
        // Handle the broadcasted event
        $message = $event->message;
        // Perform any necessary actions with the message
    }
}
