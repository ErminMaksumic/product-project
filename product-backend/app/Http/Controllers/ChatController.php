<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
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

        event(new PusherEvent($data));

        return response()->json(['message' => 'Message sent successfully', $data], 200);
    }
}
