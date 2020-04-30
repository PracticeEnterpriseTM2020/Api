<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class jobController extends Controller
{

    public function __construct()
    {
        $this->middleware("can:human-resources");
    }

    public function show_by_id(Job $job)
    {
        return $job;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "job_title" => "required|string|unique:jobs,job_title"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $job = Job::create($request->all());
        return response()->json($job, 201);
    }

    public function destroy(Request $request, Job $job)
    {
        $job->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $job = Job::withTrashed()->findOrFail($id);
        $job->restore();
        return response()->json($job, 200);
    }

    public function update(Request $request, Job $job)
    {
        $validator = Validator::make($request->all(), [
            "job_title" => "required|string|unique:jobs,job_title"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $job->update($request->all());
        return response()->json($job, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("jobs");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "key" => Rule::in($cols),
            "amount" => "integer|gt:0"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $key = $request->input("key", "job_title");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        $response = Job::where("job_title", "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);

        return collect(["sort" => $sort, "order" => $order, "search" => $search, "key" => $key])->merge($response);
    }
}
