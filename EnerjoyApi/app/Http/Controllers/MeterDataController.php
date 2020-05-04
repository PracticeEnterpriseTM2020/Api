<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\meter_customer;
use App\meter_data;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\employeeTrait;
use App\Http\Traits\customerTrait;

class MeterDataController extends Controller
{
    use employeeTrait;
    use customerTrait;
    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|numeric|exists:meters,id',
            'meterReading' => 'required|numeric|min:0|not_in:0',
            'readDate' => 'required|max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $queryGetConnectionID = meter_customer::query();
        $resultGetConnectionID = $queryGetConnectionID->select('id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->get();
        if (count($resultGetConnectionID)) {
            $resultGetConnectionID = $queryGetConnectionID->select('id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->first();

            $meter_data = new meter_data();

            $meter_data->Connection_ID = $resultGetConnectionID->id;
            $meter_data->meterReading = request('meterReading');
            $meter_data->readDate = strtotime(request('readDate'));

            if ($meter_data->save()) {
                return response()->json(['success' => true, 'message' => 'Meter data added to database.'], 200);
            } else {
                return response()->json(['success' => false, 'errors' => 'Meter data has not been added to database.'], 400);
            }
        } else {
            return response()->json(['success' => false, 'errors' => 'No connection found for given meter.'], 400);
        }
    }
}
