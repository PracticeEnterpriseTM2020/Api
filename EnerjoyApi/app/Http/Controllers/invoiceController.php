<?php

namespace App\Http\Controllers;
use Validator;
//use App\Http\Controllers\Schema;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\invoice;
use App\Http\Traits\employeeTrait;
use App\Http\Traits\customerTrait;
use App\Http\Resources\invoice as invoiceResource;


class invoiceController extends Controller
{
    
    use employeeTrait;
    //use customerTrait;
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        //Show all invoice data on the screen
        
        return invoiceResource::collection(invoice::all());
    }


    //Filtering all database entries (Code by HR team)
    public function filter(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $cols = Schema::getColumnListing("invoices");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "invoiceId" => "integer|gt:0",
            "customerId" => "integer|gt:0",
            "amount" => "integer|gt:0"
        ], ["in" => ":attribute must be one of the following types: :values"]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $customerId = $request->input("customerId", 0);
        $invoiceId = $request->input("invoiceId", 0);
        $amount = $request->input("amount", 5);

        //Show all entries
        if ($customerId == 0 && $invoiceId == 0)
        {
            return Invoice::where("active", "=", "1")
            ->orderBy($sort, $order)
            ->paginate($amount);
        }

        //Filter based on InvoiceId
        else if($customerId == 0 && $invoiceId != "0")
        {
            return Invoice::where("id", "=", "$invoiceId")
            ->where("active", "=", "1")
            ->orderBy($sort, $order)
            ->paginate($amount);
        }
        //Filter based on customerId
        else if ($customerId != 0 && $invoiceId == "0")
        {
            return Invoice::where("customerId", "=", "$customerId")
            ->where("active", "=", "1")
            ->orderBy($sort, $order)
            ->paginate($amount);
        }
        //Filter on both customer and invoice ID
        else
        {
            return Invoice::where("customerId", "=", "$customerId")
            ->where("id", "=", "$invoiceId")
            ->where("active", "=", "1")
            ->orderBy($sort, $order)
            ->paginate($amount);
        }
    }


    /*
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
    */

    
    //Store a newly created resource in storage.
    public function store(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        //Validate the input, make sure all parameters are present and correct
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'customerId' => 'required',
            'price' => 'required',
            'date' => 'required|max:16|min:16|date'
        ]);

       //If the data entered isn't valid, throw error and don't add to DB
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
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

    
    public function destroy(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(),[
            "id"=> 'required',
        ]);

        //If the data entered isn't valid, throw error and don't alter the DB
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }

        //Get the correct row
        $invoice = Invoice::where('id',$request['id'])->where('active',1)->first();
        if(!$invoice){
            return response()->json(['delete'=>false,'message'=>'Invoice could not be found'],404);
        }

        //Set the active column of the row to 0
        $invoice->active = 0;
        if(!$invoice->save()){
            return response()->json(['delete'=>false,'message'=>'Invoice could not be deleted'],422);
        }
        else{
            return response()->json(['delete'=>true,'message'=>'Invoice has been deleted']);
        }
    }
    
    public function restore(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->isEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(),[
            "id"=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }

        //Get the correct row
        $invoice = Invoice::where('id',$request['id'])->where('active',0)->first();
        if(!$invoice)
        {
        return response()->json(['restore'=>false,'message'=>'Inactive invoice could not be found'],404);
        }

        $invoice->active = 1;
        if(!$invoice->save()){
            return response()->json(['restore'=>false,'message'=>'Invoice could not be restored'],422);
        }
        else{
            return response()->json(['restore'=>true,'message'=>'Invoice has been restored']);
        }

    }

}
