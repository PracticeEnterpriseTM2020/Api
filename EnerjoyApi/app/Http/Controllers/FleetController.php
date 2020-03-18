<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Fleet;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class FleetController extends Controller
{

    public function show_all()
    {
        return Fleet::get();
    }

    public function show(Fleet $fleet)
    {
        return $fleet;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "brand" => "required|string",
            "model" => "required|string",
            "licenseplate" => "required|string|unique:fleets,licenseplate",
            "owner_id" => "nullable|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 409);

        $fleet = Fleet::create($request->all());
        return response()->json($fleet, 201);
    }

    public function destroy(Fleet $fleet)
    {
        $fleet->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $fleet = Fleet::onlyTrashed()->findOrFail($id);
        $fleet->restore();
        return response()->json($fleet, 200);
    }

    public function update(Request $request, Fleet $fleet)
    {
        $validator = Validator::make($request->all(), [
            "brand" => "string",
            "model" => "string",
            "licenseplate" => "string|unique:fleets,licenseplate",
            "owner_id" => "nullable|exists:employees,id"
        ]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 409);

        $fleet->update($request->all());
        return response()->json($fleet, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("fleets");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "key" => Rule::in($cols),
            "amount" => "integer|gt:0"
        ], ["in" => ":attribute must be one of the following types: :values"]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $key = $request->input("key", "brand");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        return Fleet::where($key, "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);
    }
}
