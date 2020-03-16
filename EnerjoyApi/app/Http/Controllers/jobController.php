<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;

class jobController extends Controller
{
    public function show_by_id(Job $job)
    {   
        return $job;
    }

    public function store(Request $request)
    {
        $job = Job::create($request->all());
        return response()->json($job,201);    
    }

    public function destroy(Request $request, Job $job)
    {
        $job->delete();
        return response()->json(null,204);
    }

    public function restore($id)
    {
        $job = Job::withTrashed()->findOrFail($id);
        $job->restore();
        return response()->json($job,200);
    }

    public function update(Request $request, Job $job)
    {
        $job->update($request->all());
        return response()->json($job,200);
    }

    public function filter(Request $request)
    {
        $sort = $request->input("sort","id");
        $order = $request->input("order","asc");
        $search = $request->input("search","");

        try
        {
            return Job::where("job_title", "like", "%$search%")
                ->orderBy($sort,$order)
                ->paginate(5);
        }
        catch(QueryException $e)
        {
            return response()->json(["message"=>"bad request"],400);
        }
    }
}