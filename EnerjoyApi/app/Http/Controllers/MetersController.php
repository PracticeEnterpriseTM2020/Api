<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Meters;

class MetersController extends Controller
{

    public function index()
    {
        $meter = Meters::get();

        return view('meters.index', ['meters' => $meter]);
    }

    public function show($meter_id)
    {
        $meter = Meters::find($meter_id);

        return view('meters.search', ['meters' => $meter]);
    }

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

        //return response()->setStatusCode(200);

        return view('meters.index', ['meters' => $meter]);
    }
}
