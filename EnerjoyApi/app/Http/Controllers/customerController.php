<?php
use \Sirprize\PostalCodeValidator\Validator;
namespace App\Http\Controllers;
use Validator;
use App\address;
use App\city;
use App\country;
use App\customer;
use App\Employee;
use App\Http\Resources\customer as customerResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Traits\employeeTrait;
use App\Http\Traits\customerTrait;

class customerController extends Controller
{
    
    use employeeTrait;
    use customerTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //#################################################################
    public function index(Request $request)
    {
        $token = $request->header('Authorization');
        if(!$this->getEmployee($token)){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        return customerResource::collection(customer::paginate(5));
    }
    //#################################################################
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request){
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
            "password"=> 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        $customer = customer::where('email',$request['email'])->where('active',0)->where('password',$request['password'])->firstOrFail();
        $customer->active = 1;
        $customer->save();
        return response()->json(['success' => true, 'message' => "customer has been activated"]);
    }
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
            'street'=>strtolower($request['street']),
            'number'=>$request['number'],
            'cityId'=>$city->id
        ]);
        $cust = customer::create([
            'lastname'=>$request['last'],
            'firstname'=>$request['first'],
            'email'=>$request['email'],
            'password'=>password_hash($request['password'], PASSWORD_BCRYPT),
            'addressId'=>$addr->id,

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
    public function show(Request $request)
    {
        $token = $request->header("Authorization");
        if($this->isCustomer($token)){
            return  new customerResource(customer::where('api_token',$token)->with('address.city','address.city.country')->FirstOrFail());
        }
        if($this->isEmployee($token)){
            $validator = Validator::make($request->all(),[
                "email"=> 'required|email'
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->messages()], 400);
            }
            return  new customerResource(customer::where('email',$request['email'])->with('address.city','address.city.country')->FirstOrFail());
        }
        return response()->json(['success'=>false,'message'=>'invalid login']);
    }
    //#################################################################
    /*public function Verify(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
            "password"=>'required|max:255'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        if(customer::where('email',$request["email"])->where('password',$request['password'])->where('active',1)->exists()){
            $cust=customer::where('email',$request["email"])->where('password',$request['password'])->FirstOrFail();
            $token=Str::random(80);
            $cust->api_token = hash('sha256',$token);
            $cust->save();
            return response()->json(['login'=>true,'token'=>$token]);
        }
        else{
            return response()->json(['login'=>false,'message'=>'customer password and email do not match']);
        }
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $customer = $this->getCustomer($request->header("Authorization"));
        if(!$customer){
            return response()->json(['success' => false, 'message' => "Not a valid Api token"], 401);
        }
        $validator = Validator::make($request->all(),[
            "newEmail"=>['required','email',
            Rule::unique('customers','email')->ignore($customer->email,'email')],
            "first"=> 'required|alpha',
            "last"=> 'required|alpha',
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
        if(!$Vpostal->hasCountry(strtoupper($request['countrycode']))){
            return response()->json(['success' => false, 'message' => "Countrycode does not exist"], 400);
        }
        $check=$Vpostal->isvalid($request['countrycode'],$request["postalcode"]);
        if(!$check){
            return response()->json(['success' => false, 'message' => "Postalcode does not exist"], 400);
        }
        $address = address::where('id',$customer->addressId)->firstOrFail();
        $country = country::where('abv',strtoupper($request["countrycode"]))->firstOrFail();
        $city = city::firstOrCreate(['name'=>strtolower($request['city']),'postalcode'=>$request['postalcode']],['name'=>strtolower($request['city']),'postalcode'=>$request['postalcode'],'countryId'=>$country->id]);
        $address->update(['street'=>strtolower($request['street']),'number'=>$request['number'],'cityId'=>$city->id]);
        $customer->update(['firstname'=>$request['first'],'lastname'=>$request['last'],'email'=>$request['newEmail']]);
        return response()->json(['success' => true, 'message' => "customer has been updated"]);
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
        if(!customer::where('api_token',$request->header('Authorization'))->exists()){
            return response()->json(['success' => false, 'message' => 'invalid login'], 401);
        }
        $customer=customer::where('api_token',$request->header('Authorization'))->first();
        $customer->active = 0;
        $customer->api_token = null;
        if(!$customer->save()){
            return response()->json(['delete'=>false,'message'=>'customer could not be deleted']);
        }
        else{
            return response()->json(['delete'=>true,'message'=>'customer has been deleted'],422);
        }
    }
    public function filter(Request $request)
    {
        $token = $request->header('Authorization');
        if(!employee::where('api_token', $token)->exists()){
            return response()->json(['success'=>false,'message'=>'invalid login']);
        }
        $validator = Validator::make($request->all(),[
            "search"=>"string|required",
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
            $search=$request['search'];
        try
        {
            return customer::where("email", "like", "%$search%")
                ->paginate(5);
        }
        catch(QueryException $e)
        {
            return response()->json(["message"=>"bad request"],400);
        }
    }
}
