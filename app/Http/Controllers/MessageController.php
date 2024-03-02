<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use Illuminate\Support\Facades\Redis;
use \Illuminate\Http\JsonResponse;

/**
 * @OA\Info(title="Message API", version="0.1")
 */
class MessageController extends Controller
{
    /**
 * @OA\Post(
 *     path="/messages/send",
 *     summary="Send a message",
 *     tags={"Messages"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="receiver_number",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="content",
 *                     type="string"
 *                 ),
 *                 required={"receiver_number", "content"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="success"
 *             )
 *         )
 *     )
 * )
 */
    public function send(SendMessageRequest $request): JsonResponse
    {
        Redis::lPush($request->receiver_number, $request->content);

        Redis::expire($request->receiver_number, 7200);

        return response()->json(['message' => 'success']);
    }

    /**
     * @OA\Get(
     *     path="/messages/receive/{receiver_number}",
     *     summary="Receive a message",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="receiver_number",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="messages",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example="['message1', 'message2']"
     *             )
     *         )
     *     )
     * )
     */
    public function receive(string $receiverNumber): JsonResponse
    {
        if (!Redis::exists($receiverNumber)) {
            return response()->json(['message' => 'No message']);
        }

        return response()->json(['messages' => Redis::lRange($receiverNumber, 0, -1)]);
    }
}
