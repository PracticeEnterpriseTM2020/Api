<?php

namespace App\Http\Controllers;
use Validator;
use App\customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
class customerAuthController extends Controller
{
    private $token;
    public function __construct()
    {
      //create unique Token
      $this->apiToken = uniqid(base64_encode(Str::random(60)));
    }
    public function Login(Request $request){
        $validator = Validator::make($request->all(),[
            "email"=> 'required|email',
            "password"=>'required|max:255'
        ]);
      
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()], 400);
        }
        //get customer based on email
        $cust=customer::where('email',$request["email"])->where('active',1)->first();
        
        if($cust){
            //verify if password is correct
            if(password_verify($request['password'],$cust->password)){
                //if correct set the apitoken
                $postArray = ['api_token' => $this->apiToken];
                $cust->api_token = $this->apiToken;
                //save customer with token
                $cust->save();
                //return some inforamtion about logged in customer
                return response()->json(['success'=>true,'message'=>[
                    'firstname' => $cust->firstname,
                    'lastname' => $cust->lastname,
                    'email' => $cust->email,
                    'token' => $this->apiToken
                ]
                ]);
            }
            else{
                return response()->json(['success'=>false,'message'=>'password email combination not found'],401);
            }
        }
        else{
            return response()->json(['success'=>false,'message'=>'password email combination not found'],401);
        }
    }
    public function logout(Request $request){
        $token = $request->header('Authorization');
        $customer = customer::where('api_token',$token)->first();
        if($customer){
            //set token back to null
            $customer->api_token = null;
            //save it
            $customer->save();
            return response()->json(['success'=>true,'message'=>'user logged out']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'user is not logged in'],401);
        }
    }
}