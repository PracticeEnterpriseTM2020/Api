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
            "email"=> 'required|email|unique:customers,email',
            "first"=> 'required|alpha',
            "last"=> 'required|alpha',
            "password"=> 'required|max:255',
            "street"=> 'required|max:70|string',
            "number"=> 'required|max:7|alpha_num',
            "city"=> 'required|string|max:30',
            "postalcode"=> 'required|max:15|alpha_dash',
            "countrycode"=>'required|max:2|alpha'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        $Vpostal = new \Sirprize\PostalCodeValidator\Validator();
        if(!$Vpostal->hasCountry($request['countrycode'])){
            return response()->json(['success' => false, 'message' => "Countrycode does not exist"], 400);
        }
        $check=$Vpostal->isvalid($request['countrycode'],$request["postalcode"]);
        if(!$check){
            return response()->json(['success' => false, 'message' => "Postalcode does not exist"], 400);
        }
        $country=country::where('abv',strtoupper($request["countrycode"]))->firstOrFail();
        if(address::leftJoin('city','addresses.cityId','=','city.id')
        ->where('street',$request["street"])
        ->where('number',$request['number'])
        ->where('city.name',$request['city'])
        ->where('city.postalcode',$request['postalcode'])
        ->where('city.countryId',$country->id)->exists()){
            return response()->json(['success' => false, 'message' => "Address already in use"], 400);
        }
        $city = city::firstOrCreate(
        [
            'name'=>strtolower($request['city']),
            'postalcode'=>$request['postalcode']
        ],
        [
            'countryId'=>$country->id,
            'name'=>strtolower($request['city']),
            'postalcode'=>$request['postalcode']
        ]);
        $addr = address::create(
        [
            'street'=>$request['street'],
            'number'=>$request['number'],
            'cityId'=>$city->id
        ]);
        $cust = customer::create([
            'lastname'=>$request['last'],
            'firstname'=>$request['first'],
            'email'=>$request['email'],
            'password'=>$request['password'],
            'addressId'=>$addr->id
        ]);
        return response()->json(['success' => true, 'message' => "Successfully created customer"]);
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
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        if(customer::where('email',$email)->exists()){
            return  new customerResource(customer::where('email',$email)->with('address.city','address.city.country')->first());
        }
        else{
            return response()->json(['success' => false, 'message' => 'customer does not exist'], 404);
        }
    }
    //#################################################################
    public function Verify(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
            "password"=>'required|max:255'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        if(customer::where('email',$request["email"])->where('password',$request['password'])->where('active',1)->exists()){
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
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        $customer = customer::where('email',$request['email'])->where('active',1)->first();
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
