<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\invoice;
use App\Http\Resources\invoice as invoiceResource;


class invoiceController extends Controller
{
    public function index()
    {
        //Show all invoice data on the screen
        return invoiceResource::collection(invoice::all());
    }


    
    public function showSingle($invoiceId)
    {
        //Validate the ID that has been entered (make sure it is a number)
        $validator = Validator::make(['id' => $invoiceId], [
            'id' => 'required|numeric'
        ]);
      
        //If the number is not valid, throw a 422 error
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }

        //Return the invoice from the requested ID
        return invoice::where('id',$invoiceId)->get();
    }

    
     //Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|max:255',
            'creation_timestamp' => 'required|max:16|min:16|date'
        ]);

        //If the data entered isn't valid, throw error and don't add to DB
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }

        $invoice = new Invoice();

        $invoice->id = request('id');
        $invoice->customerId = request('customerId');
        $invoice->price = request('price');
        $invoice->date = 
        $meter->save();

        if (!$meter->save()) {
            return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 422);
        } else {
            return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
        }
    }

}
