<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\meter_customer;
use App\Meters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\employeeTrait;
use App\Http\Traits\customerTrait;

class MeterCustomerController extends Controller
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
            'customer_email' => 'required|email|exists:customers,email',
            'meter_id' => 'required|numeric|exists:meters,id',
            'installedOn' => 'required|max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $selectQueryCheckIfMeterIsOkToUse = Meters::query();

        $selectIfMeterIsGoodToUse = $selectQueryCheckIfMeterIsOkToUse->select('id')->where('id', '=', request('meter_id'))->where('isUsed', '=', '0')->where('deleted', '=', '0')->get();

        if (count($selectIfMeterIsGoodToUse)) {

            $queryCheckIfMeterCustomerConnectionExistsOrDeleted = meter_customer::query();

            $resultCheckIfMeterCustomerConnectionExistsOrDeleted = $queryCheckIfMeterCustomerConnectionExistsOrDeleted->select('meter_id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->get();

            if (!count($resultCheckIfMeterCustomerConnectionExistsOrDeleted)) {

                $meter_customer = new meter_customer();

                $meter_customer->customer_email = request('customer_email');
                $meter_customer->meter_id = request('meter_id');
                $meter_customer->installedOn = strtotime(request('installedOn'));

                if (!$meter_customer->save()) {
                    return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 400);
                } else {


                    $queryUpdateMeterToUsed = Meters::query();
                    $queryUpdateMeterToUsed = $queryUpdateMeterToUsed->where('id', '=', request('meter_id'));
                    $updateMeterToUsed = $queryUpdateMeterToUsed->update(['isUsed' => 1]);


                    return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
                }
            } else {
                return response()->json(['success' => false, 'errors' => 'Meter is already used or does not exist'], 400);
            }
        } else {
            return response()->json(['success' => false, 'errors' => 'Meter is already used or does not exist'], 400);
        }
    }

    public function softdelete(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|numeric|exists:meter_customers,meter_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $queryCheckIfMeterExistsAndUsed = meter_customer::query();
        $resultCheckIfMeterExistsAndUsed = $queryCheckIfMeterExistsAndUsed->select('meter_id')->where('meter_id', '=', request('meter_id'))->where('deleted', '=', '0')->get();

        if (count($resultCheckIfMeterExistsAndUsed)) {

            $querySoftDeleteMeterCustomer = meter_customer::query();
            $querySoftDeleteMeterCustomer = $querySoftDeleteMeterCustomer->where('meter_id', '=', request('meter_id'));
            $updateSoftDeleteMeterCustomer = $querySoftDeleteMeterCustomer->update(['deleted' => 1]);

            $queryUpdateMeterToNotUsed = Meters::query();
            $queryUpdateMeterToNotUsed = $queryUpdateMeterToNotUsed->where('id', '=', request('meter_id'));
            $updateMeterToNotUsed = $queryUpdateMeterToNotUsed->update(['isUsed' => 0]);

            return response()->json(['success' => true, 'message' => 'Connection meter & customer removed'], 200);
        } else {
            return response()->json(['success' => false, 'errors' => 'Connection does not exist'], 400);
        }
    }

    public function search(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)&&!$this->isCustomer($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $queryGetAllMetersFromCustomerAndData = meter_customer::query();
        $responseQueryGetAllMetersFromCustomerAndData = $queryGetAllMetersFromCustomerAndData->select('meterInfo.id as id', 'meterInfo.meter_id as meter_id', 'meterInfo.creation_timestamp as creation_timestamp', 'installedOn')
            ->where('customer_email', '=', request('customer_email'))
            ->where('meter_customers.deleted', '=', '0')
            ->leftJoin('meters as meterInfo', 'meterInfo.id', '=', 'meter_customers.meter_id')
            ->get();

        if (count($responseQueryGetAllMetersFromCustomerAndData)) {

            return response()->json(['success' => true, 'message' => $responseQueryGetAllMetersFromCustomerAndData], 200);
        } else {
            return response()->json(['success' => false, 'errors' => 'error'], 400);
        }
    }
}
