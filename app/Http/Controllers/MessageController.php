<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use Illuminate\Support\Facades\Redis;
use \Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function send(SendMessageRequest $request): JsonResponse
    {
        Redis::set($request->receiver_number, $request->content, 'EX', 7200);

        return response()->json(['message' => 'success']);
    }

    public function receive(string $receiverNumber): JsonResponse
    {
        if (!Redis::exists($receiverNumber)) {
            return response()->json(['message' => 'No message']);
        }

        return response()->json(['message' => Redis::get($receiverNumber)]);
    }
}
