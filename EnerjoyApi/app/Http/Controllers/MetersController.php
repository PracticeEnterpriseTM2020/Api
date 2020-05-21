<?php

namespace App\Http\Controllers;

use Validator; //For validating the inputs
use App\Meters;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\employeeTrait;
use App\Http\Traits\customerTrait;

class MetersController extends Controller
{
    use employeeTrait;
    use customerTrait;
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|max:255|alpha_dash',
            'creation_timestamp' => 'required|max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        $meter = new Meters();

        $meter->meter_id = request('meter_id');
        $meter->creation_timestamp = strtotime(request('creation_timestamp'));

        if (!$meter->save()) {
            return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 511);
        } else {
            return response()->json(['success' => true, 'message' => 'Data added to database.'], 212);
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
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'meter_id'                  => 'max:255|alpha_dash',
            'creation_timestamp_after'  => 'max:16|min:16|date',
            'creation_timestamp_before' => 'max:16|min:16|date',
            'isUsed'                    => 'boolean',
            'amountPerPage'             => 'integer|between:3,100',
            'page'                      => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        if ($request->has('amountPerPage')) {
            $amountPerPageRequest = request('amountPerPage');
        } else {
            $amountPerPageRequest = 10;
        }

        if ($request->has('page')) {
            $pageRequest = request('page');
        } else {
            $pageRequest = 1;
        }


        $selectQuery = Meters::query();

        $selectQuery = $selectQuery->select('id', 'meter_id', 'creation_timestamp', 'isUsed');

        if ($request->has('meter_id')) {
            $selectQuery = $selectQuery->where('meter_id', 'like', '%' . request('meter_id') . '%');
        }

        if ($request->has('creation_timestamp_after')) {

            $selectQuery = $selectQuery->where('creation_timestamp', '>', strtotime(request('creation_timestamp_after')));
        }

        if ($request->has('creation_timestamp_before')) {

            $selectQuery = $selectQuery->where('creation_timestamp', '<', strtotime(request('creation_timestamp_before')));
        }

        if ($request->has('isUsed')) {

            $selectQuery = $selectQuery->where('isUsed', '=', request('isUsed'));
        }

        $selectQuery = $selectQuery->where('deleted', '=', '0');
        $selectMetersAll = $selectQuery->count();

        if ((floor($selectMetersAll / $amountPerPageRequest) + 1) >= $pageRequest) {
            $page = (int) $pageRequest;
        } else {
            $page = (floor($selectMetersAll / $amountPerPageRequest));
        }

        if ($pageRequest == 1) {
            $selectMeters = $selectQuery->limit($amountPerPageRequest)->get();
        } else {
            $selectMeters = $selectQuery->limit($amountPerPageRequest)->offset(($page - 1) * $amountPerPageRequest)->get();
        }

        if (count($selectMeters)) {
            return response()->json(['success' => true, 'results' => $selectMetersAll, 'pages' => (floor($selectMetersAll / $amountPerPageRequest) + 1), 'current_page' => $page, 'data' => $selectMeters], 213);
        } else {
            return response()->json(['success' => false, 'message' => 'No results found.'], 412);
        }
    }


    /**
     * Softdelete a meter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function softdelete(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        $checkIfMeterExistsAndIsNotUsedQuery = Meters::query();

        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('id', '=', request('id'));
        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('isUsed', '=', '0');
        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('deleted', '=', '0');

        $checkIfMeterExistsAndIsNotUsedMeters = $checkIfMeterExistsAndIsNotUsedQuery->get();

        if (count($checkIfMeterExistsAndIsNotUsedMeters)) {
            $updateMetersQuery = Meters::query();

            $updateMetersQuery = $updateMetersQuery->where('id', '=', request('id'), 'and', 'isUsed', '=', '0', 'and', 'deleted', '=', '0');

            $updateMeters = $updateMetersQuery->update(['deleted' => 1]);

            return response()->json(['success' => true, 'message' => 'Data successfully softdeleted.'], 214);
        } else {
            return response()->json(['success' => false, 'message' => 'id does not exist or meter is used.'], 412);
        }
    }


    /**
     * Edit a meter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'meter_id' => 'max:255|alpha_dash',
            'creation_timestamp' => 'max:16|min:16|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 411);
        }

        $checkIfMeterExistsAndIsNotUsedQuery = Meters::query();

        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('id', '=', request('id'));
        $checkIfMeterExistsAndIsNotUsedQuery = $checkIfMeterExistsAndIsNotUsedQuery->where('deleted', '=', '0');

        $checkIfMeterExistsAndIsNotUsedMeters = $checkIfMeterExistsAndIsNotUsedQuery->get();

        if (count($checkIfMeterExistsAndIsNotUsedMeters)) {
            $updateMetersQuery = Meters::query();

            $updateMetersQuery = $updateMetersQuery->where('id', '=', request('id'), 'and', 'deleted', '=', '0');

            if (($request->has('id')) && (($request->has('meter_id')) || ($request->has('creation_timestamp')))) {
                if ($request->has('meter_id')) {
                    $updateMeters = $updateMetersQuery->update(['meter_id' => request('meter_id')]);
                }
                if ($request->has('creation_timestamp')) {
                    $updateMeters = $updateMetersQuery->update(['creation_timestamp' => strtotime(request('creation_timestamp'))]);
                }
                return response()->json(['success' => true, 'message' => 'Data successfully updated in database.'], 215);
            } else {
                return response()->json(['success' => false, 'errors' => 'Request does not have an id and-or doesn\'t have a meter_id or creation_timestamp.'], 411);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'id does not exist'], 412);
        }
    }
}
