<?php
    namespace App\Http\Traits;
    use App\customer;

    trait customerTrait{
        function getCustomer($token){
            $cust=customer::where('api_token',$token)->first();
            if($cust){
                return $cust;
            }
            return false;
        }
        function isCustomer($token){
            if(customer::where('api_token',$token)->exists()){
                return true;
            }
            return false;
        }
    }
?>