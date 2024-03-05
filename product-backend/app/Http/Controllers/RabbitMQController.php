<?php

namespace App\Http\Controllers;

use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RabbitMQController extends Controller
{
    protected $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    public function publishMessage(Request $request)
    {
        $user = Auth::user();
        $message = $request->input('message');
        $sentDate = now();

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'message' => $message,
            'sentDate' => $sentDate
        ];

        $this->rabbitMQService->publish(json_encode($data));

        return response()->json(['message' => 'Message published to RabbitMQ'], 200);
    }

    public function fetchMessages()
    {
        $messages = $this->rabbitMQService->consume();
        return response()->json(['messages' => $messages], 200);
    }
}
