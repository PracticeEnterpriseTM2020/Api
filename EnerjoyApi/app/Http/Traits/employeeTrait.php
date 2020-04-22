<?php
    namespace App\Http\Traits;
    use App\Employee;

    trait employeeTrait{
        function getEmployee($token){
            $employee=employee::where('api_token',$token)->first();
            if($employee){
                return $employee;
            }
            return false;
        }
        public function isEmployee($token){
            if(employee::where('api_token',$token)->exists()){
                return true;
            }
            return false;
        }
    }
?>