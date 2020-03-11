<?php
use \Sirprize\PostalCodeValidator\Validator;
namespace App\Http\Controllers;
use Validator;
use App\address;
use App\city;
use App\country;
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
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email|exists:customers,email',
            "first"=> 'required|alpha',
            "last"=> 'required|alpha',
            "password"=> 'required|max:255',
            "street"=> 'required|max:70|alpha',
            "number"=> 'required|max:7|alpha_num',
            "city"=> 'required|alpha|max:30',
            "postalcode"=> 'required|max:15|alpha_dash',
            "countrycode"=>'required|max:2|alpha'
        ]);
        $Vpostal = new \Sirprize\PostalCodeValidator\Validator();
        $check=$Vpostal->isvalid($request['countrycode'],$request["postalcode"]);
        if(!$check){
            return response()->json(['success' => false, 'errors' => "Postalcode does not exist"], 400);
        }
        $country=country::where('abv',$request["countrycode"])->first();
        $city = new city();
        $city->name = $request["city"];
        $city->postalcode = $request["postalcode"];
        $city->countryId = $country->id;
        $city->save();
        $addr = new address();
        $addr->street = $request["street"];
        $addr->number = $request['number'];
        $addr->cityId = $city->id;
        $addr->save();
        $cust = new customer();
        $cust->firstname = $request['first'];
        $cust->lastname = $request['last'];
        $cust->email = $request['email'];
        $cust->password = $request['password'];
        $cust->addressId = $addr->id;
        $cust->save();
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
