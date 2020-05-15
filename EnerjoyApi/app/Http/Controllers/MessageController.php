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

        return response()->json($message, 201);
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

    public function update(Request $request, Message $message)
    {
        $validator = Validator::make($request->all(), [
            "text" => "required|string"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $message->update($request->all());
        return response()->json($message, 200);
    }

    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "conversation_id" => "required|integer|exists:conversations,id",
            "search" => "nullable|string"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $search = $request->input("search", "");
        $conversation = $request->input("conversation_id");

        $messages = Message::where("conversation_id", $conversation)
            ->where("text", "like", "%$search%")
            ->orderBy("created_at", "desc")
            ->paginate(15);

        return collect(["search" => $search])->merge($messages);
    }
}
