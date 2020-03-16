<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Fleet;
use Illuminate\Database\QueryException;

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

    public function store(Request $request){
        $fleet = Fleet::create($request->all());
        return response()->json([$fleet], 201);
    }

    public function destroy(Fleet $fleet){
        $fleet->delete();
        return response()->json(null, 204);
    }

    public function restore($id){
        $fleet = Fleet::onlyTrashed()->findOrFail($id);
        $fleet->restore();
        return response()->json($fleet,200);
    }
    
    public function update(Request $request, Fleet $fleet){
        $fleet->update($request->all());
        return response()->json($fleet, 200);
    }

    public function filter(Request $request){
        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $search_key = $request->input("search_key", "brand");
        $search = $request->input("search", "");
        //$itemPage = $request->input("itemPerPage", 5);
        
        try{
            return Fleet::where($search_key, "like", "%$search%")
                    ->orderBy($sort, $order)
                    ->get();
        }catch(QueryException $e){
            return response()->json(['success' => false, 'message' => "bad request"], 400);
        }
    }
}
