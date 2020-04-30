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
