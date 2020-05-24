<?php
    namespace App\Http\Traits;
    use App\customer;

    trait customerTrait{
        
        function getCustomer($token){
            //get the customer based on token.
            //can be used if customer should only be able to get specific rescource
            $cust=customer::where('api_token',$token)->first();
            if($cust){
                return $cust;
            }
            //if no customer with specified token is found
            //return false
            return false;
        }
        function isCustomer($token){
            //cehck if customer exists
            if(customer::where('api_token',$token)->exists()){
                return true;
            }
            //if not return false
            return false;
        }
    }
?>