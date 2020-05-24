<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JobOffer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Validator;

class JobOfferController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:human-resources")->except(["filter", "show"]);
    }

    public function show(JobOffer $job_offer)
    {
        return $job_offer;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "job_offer_title" => "required|string",
            "job_offer_description" => "required|string|max:65535",
            "job_id" => "required|exists:jobs,id",
            "creator_id" => "required|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $job_offer = JobOffer::create($request->all())->load(["creator", "job"]);
        return response()->json($job_offer, 201);
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
        $validator = Validator::make($request->all(), [
            "job_offer_title" => "required|string",
            "job_offer_description" => "required|string|max:65535",
            "job_id" => "required|exists:jobs,id",
            "creator_id" => "required|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $job_offer->update($request->all());
        return response()->json($job_offer, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("job_offers");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "key" => Rule::in($cols),
            "amount" => "integer|gt:0"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $key = $request->input("key", "job_offer_title");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        $response = JobOffer::where($key, "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);

        return collect(["sort" => $sort, "order" => $order, "search" => $search, "key" => $key])->merge($response);
    }
}
