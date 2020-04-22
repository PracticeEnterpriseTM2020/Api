<?php

namespace App\Http\Controllers;
use Validator;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
class employeeAuthController extends Controller
{
    
    private $token;
    public function __construct()
    {
      // Unique Token
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
        $employee=employee::where('email',$request["email"])->where('deleted_at',null)->first();
        if($employee){
            if($employee->api_token != null){
                return response()->json(['success' => false, 'message' => 'already logged in'], 400);
            }
            if(password_verify($request['password'],$employee->password)){
                $postArray = ['api_token' => $this->apiToken];
                $employee->api_token = $this->apiToken;
                $employee->save();
                return response()->json(['success'=>true,'message'=>[
                    'firstname' => $employee->first_name,
                    'lastname' => $employee->last_name,
                    'email' => $employee->email,
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
        $employee = employee::where('api_token',$token)->first();
        if($employee){
            $employee->api_token = null;
            $employee->save();
            return response()->json(['success'=>true,'message'=>'user logged out']);
        }
        else{
            return response()->json(['success'=>false,'message'=>'user is not logged in'],401);
        }
    }
}
