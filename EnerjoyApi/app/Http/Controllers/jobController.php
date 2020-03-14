<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;

class jobController extends Controller
{
    public function show_all()
    {
        return Job::paginate(5);
    }

    public function show_by_id($id)
    {   
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ]); 

        //als je variable leeg laat "page not found" ipv "error"??

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $job = Job::find($id);

        if(count($job))
        {
            return $job;
        }else
        {
            return response()->json(['success' => false, 'errors' => "No results found"], 400);
        }
    }

    public function store(Request $request)
    {
        //validate
        $rules = array(
            'job_title'    => 'required'
        );

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }
        else
        {
            $job = new Job;

            $job->id = request('id');
            $job->job_title   = $request->input('job_title');

            if($job->save())
            {
                return response()->json(['success' => true, 'errors' => "Successfully added to the database"], 200);
            }
            else
            {
                return response()->json(['success' => false, 'errors' => "Unable to add to the database "], 400);
            }
        }
        
    }

    public function destroy($title)
    {
        $validator = Validator::make(['job_title' => $title], [
            'job_title' => 'required'
        ]); 

        if($validator->fails())
        {
            return response()->json(['delete' => false, 'errors' => $validator->messages()], 400);
        }

        $job = Job::where('job_title', $title)->first();
        if(!$job)
        {
            return response()->json(['delete'=>false,'message'=>'Could not find employee'],404);
        }else
        {
            $job->delete();
            if($job->trashed())
            {
                return response()->json(['delete'=>true,'message'=>'Employee soft-deleted from database.'],200);
            }
            else
            {
                return response()->json(['delete'=>false,'message'=>"Could not soft-delete employee."],404);
            }
        }
    }

    public function restore($title)
    {
        $validator = Validator::make(['job_title' => $title], [
            'job_title' => 'required'
        ]); 

        if($validator->fails())
        {
            return response()->json(['restore' => false, 'errors' => $validator->messages()], 400);
        }

        $job = Job::onlyTrashed()->where('job_title', $title)->first();
        if(!$job)
        {
            return response()->json(['restore'=>false,'message'=>'Could not find job'],404);
        }else
        {
            if($job->restore())
            {
                return response()->json(['restore'=>true,'message'=>'Job restored.'],200);
            }
            else
            {
                return response()->json(['restore'=>false,'message'=>"Could not restore job."],404);
            }
        }
    }

    public function update(Request $request)
    {
        $job = Job::find($request->input("id"));

        $rules = array(
            "id"                   => 'required|integer',
            'job_title'            => 'string'
        );

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        if(!count($job))
        {
            return response()->json(['success' => false, 'errors' => "Job not found"], 400);
        }else
        {
            $counter = 0;

            if($job->job_title != $request->input("job_title") && !empty($request->input("job_title")))
            {
                $job->job_title = $request->input("job_title");
                $counter++;
            }

            if($counter != 0)
            {
                if($job->save())
                {
                    return response()->json(['success' => true, 'errors' => "Successfully updated the database"], 200);
                }
                else
                {
                    return response()->json(['success' => false, 'errors' => "Unable to update the database "], 400);
                }
            }
            else
            {
                return response()->json(['success' => false, 'errors' => "No update needed."], 400);
            }
        }
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
