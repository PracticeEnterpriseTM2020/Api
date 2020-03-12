<?php

namespace App\Http\Controllers;

use Validator;
use Request;
use App\Http\Controllers\Controller;
use App\Employee;

class employeeController extends Controller
{
    public function show_all()
    {
        return Employee::get();
    }

    public function show_by_id($id)
    {   
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer'
        ]); 

        //als je variable leeg laat "page not found" ipv "error"??

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        $employee = Employee::find($id);

        if(count($employee))
        {
            return $employee;
        }else
        {
            return response()->json(['success' => false, 'errors' => "No results found"], 400);
        }
    }

    public function store(Request $request)
    {
        //validate
        $rules = array(
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email',
            'password'      => 'required',
            'salary'        => 'required|numeric',
            'address_id'    => 'required|integer',
            'job_id'        => 'required|integer'
        );

        $validator = Validator::make($request::all(),$rules);

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }
        else
        {
            $employee = new Employee;

            $employee->id = request('employee_id');
            $employee->first_name   = $request::get('first_name');
            $employee->last_name    = $request::get('last_name');
            $employee->email        = $request::get('email');
            $employee->password     = bcrypt($request::get('password'));
            $employee->salary       = $request::get('salary');
            $employee->address_id   = $request::get('address_id');
            $employee->job_id       = $request::get('job_id');

            if($employee->save())
            {
                return response()->json(['success' => true, 'errors' => "Successfully added to the database"], 200);
            }
            else
            {
                return response()->json(['success' => false, 'errors' => "Unable to add to the database "], 400);
            }
        }
        
    }

    public function destroy($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]); 

        if($validator->fails())
        {
            return response()->json(['delete' => false, 'errors' => $validator->messages()], 400);
        }

        $employee = Employee::where('email', $email)->first();
        if(!$employee)
        {
            return response()->json(['delete'=>false,'message'=>'Could not find employee'],404);
        }else
        {
            if($employee->active != 0)
            {
                $employee->active = 0;
                if($employee->save())
                {
                    return response()->json(['delete'=>true,'message'=>'Employee deleted from database']);
                }
                else
                {
                    return response()->json(['delete'=>false,'message'=>'Employee could not be deleted from database'],400);
                }
            }
            else
            {
                return response()->json(['delete'=>false,'message'=>'Employee already deleted from database'],422);
            }
            
        }
    }

    public function create()
    {

    }
}
