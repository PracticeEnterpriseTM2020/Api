<?php

namespace App\Http\Controllers;

use App\country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Validator;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:human-resources");
    }

    public function show(country $country)
    {
        return $country;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nicename" => "required|string",
            "iso" => "required|string|max:2|min:2|unique",
            "iso3" => "required|string|max:3|min:3|unique",
            "phonecode" => "required|numeric",
            "numcode" => "numeric"
        ]);

        $request->request->add(['name'=> $request->nicename]);

        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $country = country::create($request->all());
        return response()->json($country, 201);
    }

    public function destroy(country $country)
    {
        $country->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $country = country::onlyTrashed()->findOrFail($id);
        $country->restore();
        return response()->json($country, 200);
    }

    public function update(Request $request, country $country)
    {
        $country->update($request->all());
        return response()->json($country, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("countries");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "amount" => "integer|gt:0"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        $response = country::where("name", "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);

        return collect(["sort" => $sort, "order" => $order, "search" => $search])->merge($response);
    }
}
