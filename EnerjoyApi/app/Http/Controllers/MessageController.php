<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
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
        return $message;
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();
        return response()->json($message, 200);
    }
}
