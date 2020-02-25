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
    public function index()
    {
        return customerResource::collection(customer::all());
    }
    public function indexLogin()
    {
        return customer::select('email','password')->get();
    }

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
    public function show($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
        }
        return customer::where('email',$email)->with('address.city','address.city.country')->get();
    }
    public function showLogin($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
          ]);
      
          if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 422);
          }
        return customer::where('email',$email)->select('email','password')->get();
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
    public function destroy($id)
    {
        //
    }
}
