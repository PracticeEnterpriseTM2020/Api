<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\meter_customer;
use App\Meters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeterCustomerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email',
            'meter_id' => 'required|numeric|exists:meters,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $selectQueryCheckIfMeterIsOkToUse = Meters::query();

        $selectIfMeterIsGoodToUse = $selectQueryCheckIfMeterIsOkToUse->select('id')->where('id', '=', request('meter_id'))->where('isUsed', '=', '0')->where('deleted', '=', '0')->get();

        if (count($selectIfMeterIsGoodToUse)) {

            $queryCheckIfMeterCustomerConnectionExcistsOrDeleted = meter_customer::query();

            $resultCheckIfMeterCustomerConnectionExcistsOrDeleted = $queryCheckIfMeterCustomerConnectionExcistsOrDeleted->select('meter_id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '1')->get();

            if (count($resultCheckIfMeterCustomerConnectionExcistsOrDeleted)) {

                return response()->json(['success' => false, 'errors' => 'Meter is already used or does not exist'], 400);
            } else {
                $meter_customer = new meter_customer();

                $meter_customer->customer_email = request('customer_email');
                $meter_customer->meter_id = request('meter_id');
                //$meter_customer->save();

                if (!$meter_customer->save()) {
                    return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 400);
                } else {


                    $queryUpdateMeterToUsed = Meters::query();
                    $queryUpdateMeterToUsed = $queryUpdateMeterToUsed->where('id', '=', request('meter_id'));
                    $updateMeterToUsed = $queryUpdateMeterToUsed->update(['isUsed' => 1]);


                    return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
                }
            }
        } else {
            return response()->json(['success' => false, 'errors' => 'Meter is already used or does not exist'], 400);
        }
    }

    public function softdelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|numeric|exists:meter_customer,meter_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $checkIfMeterExistsAndIsNotUsedQuery = meter_customer::query();

        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('id', '=', request('id'));
        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('isUsed', '=', '0');
        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('deleted', '=', '0');

        $checkIfMeterExistsAndIsNotUsedMeters = $checkIfMeterExistsAndIsNotUsedQuery->get();

        if (count($checkIfMeterExistsAndIsNotUsedMeters)) {
            $updateMetersQuery = Meters::query();

            $updateMetersQuery = $updateMetersQuery->where('id', '=', request('id'), 'and', 'isUsed', '=', '0', 'and', 'deleted', '=', '0');

            $updateMeters = $updateMetersQuery->update(['deleted' => 1]);

            return response()->json(['success' => true, 'message' => 'Meter deleted.'], 200);
        } else {
            return response()->json(['success' => false, 'errors' => 'id does not exist or meter is used.'], 400);
        }
    }
}
