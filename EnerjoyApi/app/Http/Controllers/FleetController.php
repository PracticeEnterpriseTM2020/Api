<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Fleet;
use ErrorException;
use Illuminate\Database\QueryException;
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
        $fleet = Fleet::create($request->all());
        return response()->json([$fleet], 201);
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
        $fleet->update($request->all());
        return response()->json($fleet, 200);
    }

    public function filter(Request $request)
    {
        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $key = $request->input("key", "brand");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        try {
            return Fleet::where($key, "like", "%$search%")
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
