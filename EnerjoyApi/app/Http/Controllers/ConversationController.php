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
            "search" => "string"
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
        // TODO: check for unique combination 
        $validator = Validator::make($request->all(), [
            "employee_one" => "required|integer|exists:employees,id",
            "employee_two" => "required|integer|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $emp1 = $request->input("employee_one");
        $emp2 = $request->input("employee_two");
        if ($emp1 > $emp2) {
            $temp = $emp1;
            $emp1 = $emp2;
            $emp2 = $temp;
        }

        $conversation = Conversation::create(["employee_one_id" => $emp1, "employee_two_id" => $emp2]);
        return $conversation;
    }
}
