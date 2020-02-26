<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Meters;

class MetersController extends Controller
{
    public function create()
    {
        return view('meters.create');
    }

    public function store()
    {
        request()->validate([
            'meter_id' => 'required',
            'creation_timestamp' => 'required'
        ]);

        //dump(request()->all());

        $meter = new Meters();

        $meter->meter_id = request('meter_id');
        $meter->creation_timestamp = strtotime(request('creation_timestamp'));
        $meter->save();

        return response()->json([
            'response_code' => 200,
            'response_message' => 'Data added successfully!'
        ]);
    }
}
