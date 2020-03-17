<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Job;
use App\JobOffer;
use ErrorException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Validator;

class JobOfferController extends Controller
{

    public function show_all()
    {
        return JobOffer::get();
    }

    public function show(JobOffer $job_offer)
    {
        return $job_offer;
    }

    public function store(Request $request)
    {
        //return $request->all();
        $job_offer = JobOffer::create($request->all());
        return response()->json([$job_offer], 201);
    }

    public function destroy(JobOffer $job_offer)
    {
        $job_offer->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $job_offer = JobOffer::onlyTrashed()->findOrFail($id);
        $job_offer->restore();
        return response()->json($job_offer, 200);
    }

    public function update(Request $request, JobOffer $job_offer)
    {
        $job_offer->update($request->all());
        return response()->json($job_offer, 200);
    }

    public function filter(Request $request)
    {
        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $key = $request->input("key", "job_offer_title");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        try {
            return JobOffer::where($key, "like", "%$search%")
                ->orderBy($sort, $order)
                ->paginate($amount);
        } catch (QueryException $e) {
            return response()->json(["message" => "Bad Request: sort or key does not exist"], 400);
        } catch (ErrorException $e) {
            return response()->json(["message" => "Bad Request: amount must be a numeric value"], 400);
        } catch (InvalidArgumentException $e) {
            return response()->json(["message" => "Bad Request: order must be asc or desc"], 400);
        }
    }
}
