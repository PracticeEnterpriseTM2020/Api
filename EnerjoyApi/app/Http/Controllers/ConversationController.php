<?php

namespace App\Http\Controllers;

use Validator;
use App\Conversation;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;


class ConversationController extends Controller
{

    public function getById(Conversation $conversation)
    {
        Gate::authorize("read-conversation", $conversation);
        return $conversation;
    }

    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => "nullable|string"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $user = $request->user();
        $search = $request->input("search", "");
        $searchIds = Employee::where("first_name", "like", "%$search%")
            ->orWhere("last_name", "like", "%$search%")
            ->where("id", "!=", $user->id)
            ->pluck("id")
            ->toArray();

        $response = Conversation::where(function ($query) use ($user) {
            $query->where("employee_one_id", $user->id)
                ->orWhere("employee_two_id", $user->id);
        })
            ->where(function ($query) use ($searchIds) {
                $query->whereIn("employee_one_id", $searchIds)
                    ->orWhereIn("employee_two_id", $searchIds);
            })
            ->orderBy("updated_at", "desc")
            ->orderBy("created_at", "desc")
            ->paginate(15);

        return collect(["search" => $search])->merge($response);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "receiver_email" => "required|string|exists:employees,email"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $emp1 = $request->user();
        $emp2 = Employee::where("email", $request->input("receiver_email"))->first();

        if ($emp1->id > $emp2->id) {
            $temp = $emp1;
            $emp1 = $emp2;
            $emp2 = $temp;
        }

        if (Conversation::where("employee_one_id", $emp1)->where("employee_two_id", $emp2)->exists())
            return response()->json(["error" => trans("errors.conversation")], 400);

        $conversation = Conversation::create(["employee_one_id" => $emp1->id, "employee_two_id" => $emp2->id]);
        return response()->json($conversation, 201);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $conversation = Conversation::onlyTrashed()->findOrFail($id);
        $conversation->restore();
        return response()->json($conversation, 200);
    }
}
