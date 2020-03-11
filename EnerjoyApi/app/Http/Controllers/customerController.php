<?php

namespace App\Http\Controllers;
use Validator;
use App\address;
use App\customer;
use App\Http\Resources\customer as customerResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class customerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //#################################################################
    public function index()
    {
        return customerResource::collection(customer::all());
    }
    //#################################################################
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //#################################################################
    public function show($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);     
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }
        return  new customerResource(customer::where('email',$email)->with('address.city','address.city.country')->first());
    }
    //#################################################################
    public function Verify(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
            "password"=>'required|max:255'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }
        if(customer::where('email',$request["email"])->where('password',$request['password'])->exists()){
            return response()->json(['login'=>true,'message'=>'customer password and email match']);
        }
        else{
            return response()->json(['login'=>false,'message'=>'customer password and email do not match']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //#################################################################
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }
        $customer = customer::where('email',$request['email'])->first();
        if(!$customer){
            return response()->json(['delete'=>false,'message'=>'customer could not be found'],404);
        }
        $customer->active = 0;
        if(!$customer->save()){
            return response()->json(['delete'=>false,'message'=>'customer could not be deleted'],422);
        }
        else{
            return response()->json(['delete'=>true,'message'=>'customer has been deleted']);
        }
    }
}
