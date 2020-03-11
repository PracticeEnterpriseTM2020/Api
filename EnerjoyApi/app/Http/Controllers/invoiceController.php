<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
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
        //Validate the input, make sure all parameters are present and correct
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'customerId' => 'required',
            'price' => 'required',
            'date' => 'required|max:16|min:16|date'
        ]);

        //If the data entered isn't valid, throw error and don't add to DB
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }

        //Create a new invoice and fill the vars with the correct values
        $invoice = new Invoice();

        $invoice->id = request('id');
        $invoice->customerId = request('customerId');
        $invoice->price = request('price');
        $invoice->date = strtotime(request('date'));

        //Save the invoice to the DB
        $invoice->save();

        if (!$invoice->save()) {
            return response()->json(['success' => false, 'errors' => 'Data has not been added to database.'], 422);
        } else {
            return response()->json(['success' => true, 'message' => 'Data added to database.'], 200);
        }
    }

}
