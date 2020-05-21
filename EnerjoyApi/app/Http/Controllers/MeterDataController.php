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
        if (!$this->isEmployee($token)) {
            return response()->json(['success' => false, 'message' => 'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|numeric|exists:meters,id',
            'meterReading' => 'required|numeric|min:0|not_in:0|max:999999',
            'readDate' => 'required|max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        if (strtotime(request('readDate')) > time()) {
            return response()->json(['success' => false, 'errors' => 'Given date-time is newer then current date-time.'], 411);
        }



        $queryGetConnectionID = meter_customer::query();
        $resultGetConnectionID = $queryGetConnectionID->select('id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->get();
        if (count($resultGetConnectionID)) {
            $resultGetConnectionID = $queryGetConnectionID->select('id', 'installedOn')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->first();


            $queryCheckIfAnyReadingExists = meter_data::query();
            $resultCheckIfAnyReadingExists = $queryCheckIfAnyReadingExists->select('Connection_ID')->where('Connection_ID', '=', $resultGetConnectionID->id)->get();

            if (count($resultCheckIfAnyReadingExists)) {

                $queryMeterDataInfoLatest = meter_data::query();
                $getMeterDataInfoLatest = $queryMeterDataInfoLatest->select('totalMeterReading', 'readDate')->where('Connection_ID', '=', $resultGetConnectionID->id)->orderBy('readDate', 'DESC')->first();

                if (strtotime(request('readDate')) < $getMeterDataInfoLatest->readDate) {
                    return response()->json(['success' => false, 'errors' => 'Given date-time is older then latest reading.'], 411);
                }


                //Check if current usage is higher then previous usage known in database. If it is lower return a message.
                if (!(request('meterReading') >= $getMeterDataInfoLatest->totalMeterReading)) {
                    return response()->json(['success' => false, 'errors' => 'Meter usage is less then previous usage.'], 411);
                }

                //Calculate current usage.
                $currentUsage = request('meterReading') - $getMeterDataInfoLatest->totalMeterReading;
                $totalUsage = $getMeterDataInfoLatest->totalMeterReading + $currentUsage;

                //Calculate if current usage is to high, return a message.
                if ($currentUsage > 75) {
                    return response()->json(['success' => false, 'errors' => 'Meter usage is to high. Contact support.'], 512);
                }

                //Add usage to database
                $meter_data = new meter_data();

                $meter_data->Connection_ID = $resultGetConnectionID->id;
                $meter_data->meterReading = $currentUsage;
                $meter_data->totalMeterReading = $totalUsage;
                $meter_data->startReadDate = $getMeterDataInfoLatest->readDate;
                $meter_data->readDate = strtotime(request('readDate'));

                if ($meter_data->save()) {
                    return response()->json(['success' => true, 'message' => 'Meter data added to database.'], 212);
                } else {
                    return response()->json(['success' => false, 'errors' => 'Meter data has not been added to database. Something went wrong with saving to the database'], 511);
                }
            } else {
                //If meter has no usage yet it needs the installed on date and use that as start date.
                $meter_data = new meter_data();

                $meter_data->Connection_ID = $resultGetConnectionID->id;
                $meter_data->meterReading = request('meterReading');
                $meter_data->totalMeterReading = request('meterReading');
                $meter_data->startReadDate = $resultGetConnectionID->installedOn;
                $meter_data->readDate = strtotime(request('readDate'));

                if ($meter_data->save()) {
                    return response()->json(['success' => true, 'message' => 'Meter data added to database.'], 212);
                } else {
                    return response()->json(['success' => false, 'errors' => 'Meter data has not been added to database. Something went wrong with saving to the database'], 511);
                }
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No connection found for given meter.'], 412);
        }
    }

    public function show(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)&&!$this->isCustomer($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id'              => 'required|numeric|exists:meters,id',
            'data_timestamp_after'  => 'max:16|min:16|date',
            'data_timestamp_before' => 'max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        $queryGetConnectionIDIfMeterAndCustomerHaveConnection = meter_customer::query();
        $resultGetConnectionIDIfMeterAndCustomerHaveConnection = $queryGetConnectionIDIfMeterAndCustomerHaveConnection->select('id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->get();
        if (count($resultGetConnectionIDIfMeterAndCustomerHaveConnection)) {

            $resultGetConnectionIDIfMeterAndCustomerHaveConnection = $queryGetConnectionIDIfMeterAndCustomerHaveConnection->select('id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->first();

            $selectDataQuery = meter_data::query();

            $selectDataQuery = $selectDataQuery->select('meterReading')->where('Connection_ID', '=', $resultGetConnectionIDIfMeterAndCustomerHaveConnection->id);

            if ($request->has('data_timestamp_after')) {

                $selectDataQuery = $selectDataQuery->where('readDate', '>', strtotime(request('data_timestamp_after')));
            }

            if ($request->has('data_timestamp_before')) {

                $selectDataQuery = $selectDataQuery->where('readDate', '<', strtotime(request('data_timestamp_before')));
            }

            $countDataQuery = $selectDataQuery->get();
            $resultDataQuery = $selectDataQuery->sum('meterReading');

            if (count($countDataQuery)) {
                return response()->json(['success' => true, 'usage' => $resultDataQuery], 213);
            } else {
                return response()->json(['success' => false, 'message' => 'No results found.'], 412);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No data or connection found for given meter.'], 412);
        }
    }
}
