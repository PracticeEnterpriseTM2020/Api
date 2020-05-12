<?php

namespace App\Http\Controllers;

use App\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use Illuminate\Support\Facades\App;
use Validator;

class MessageController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "text" => "required|string",
            "conversation_id" => "required|integer|exists:conversations,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sender = $request->user();

        $message = Message::create(array_merge($request->all(), ["sender_id" => $sender->id]));

        $message->conversation()->touch();

        return $message;
    }

    public function update(Request $request, Message $message)
    {
        $validator = Validator::make($request->all(), [
            "text" => "required|string"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $message->update($request->all());
        return response()->json($message, 200);
    }
}
