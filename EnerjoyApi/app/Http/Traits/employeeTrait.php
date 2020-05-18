<?php
    namespace App\Http\Traits;
    use App\Employee;

    trait employeeTrait{
        
        function getEmployee($token){
            //get employee based on token
            //can be used when user should only be able to access specific resource
            $employee=employee::where('api_token',$token)->first();
            if($employee){
                return $employee;
            }
            return false;
        }
        public function isEmployee($token){
            //check if employee exists
            if(employee::where('api_token',$token)->exists()){
                return true;
            }
            //if no employee is found return false
            return false;
        }
    }
?>