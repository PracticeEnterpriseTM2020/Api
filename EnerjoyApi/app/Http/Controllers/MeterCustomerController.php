<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\meter_customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeterCustomerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|max:255|alpha_dash',
            'creation_timestamp' => 'required|max:16|min:16|date'
        ]);


        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $meter = new Meters();

        $meter->meter_id = request('meter_id');
        $meter->creation_timestamp = strtotime(request('creation_timestamp'));
        $meter->save();

        if (!$meter->save()) {
            return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 400);
        } else {
            return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
        }
    }
}
