<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\Meters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MetersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|max:255|alpha_dash',
            'creation_timestamp' => 'required|max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }

        $meter = new Meters();

        $meter->meter_id = request('meter_id');
        $meter->creation_timestamp = strtotime(request('creation_timestamp'));
        $meter->save();

        if (!$meter->save()) {
            return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 422);
        } else {
            return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Meters  $meters
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'max:255|alpha_dash',
            'creation_timestamp_after' => 'max:16|min:16|date',
            'creation_timestamp_before' => 'max:16|min:16|date',
            'isUsed' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }

        $selectQuery = Meters::query();

        if($request->has('meter_id'))
        {
           $selectQuery = $selectQuery->where('meter_id', 'like', '%' . request('meter_id') . '%');
        }

        if($request->has('creation_timestamp_after'))
        {

            $selectQuery = $selectQuery->where('creation_timestamp', '>', strtotime(request('creation_timestamp_after')));
        }

        if($request->has('creation_timestamp_before'))
        {

            $selectQuery = $selectQuery->where('creation_timestamp','<' , strtotime(request('creation_timestamp_before')));
        }

        if($request->has('isUsed'))
        {

            $selectQuery = $selectQuery->where('isUsed', '=', request('isUsed'));
        }
        
        $selectMeters = $selectQuery->get();
        //return $selectMeters;

        //$selectMeters = Meters::where('meter_id', 'like', '%' . $meter_id . '%')->get();

        if (count($selectMeters))
        {
            return $selectMeters;
        } else {
            return response()->json(['success' => false, 'errors' => 'No results found.'], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Meters  $meters
     * @return \Illuminate\Http\Response
     */
    public function edit(Meters $meters)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Meters  $meters
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meters $meters)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meters  $meters
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meters $meters)
    {
        //
    }
}
