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
}
