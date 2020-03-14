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
            $employee->delete();
            if($employee->trashed())
            {
                return response()->json(['delete'=>true,'message'=>'Employee soft-deleted from database.'],200);
            }
            else
            {
                return response()->json(['delete'=>false,'message'=>"Could not soft-delete employee."],404);
            }
        }
    }

    public function restore($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]); 

        if($validator->fails())
        {
            return response()->json(['restore' => false, 'errors' => $validator->messages()], 400);
        }

        $employee = Employee::onlyTrashed()->where('email', $email)->first();
        if(!$employee)
        {
            return response()->json(['restore'=>false,'message'=>'Could not find employee'],404);
        }else
        {
            if($employee->restore())
            {
                return response()->json(['restore'=>true,'message'=>'Employee restored.'],200);
            }
            else
            {
                return response()->json(['restore'=>false,'message'=>"Could not restore employee."],404);
            }
        }
    }

    public function update(Request $request)
    {
        $employee = Employee::find($request::get("id"));

        $rules = array(
            'id'            => 'required|integer',
            'first_name'    => 'string',
            'last_name'     => 'string',
            'email'         => 'email',
            'salary'        => 'numeric',
            'address_id'    => 'integer',
            'job_id'        => 'integer'
        );

        $validator = Validator::make($request::all(),$rules);
        $counter = 0;

        if($validator->fails())
        {
            return response()->json(['success' => false, 'errors' => $validator->messages()], 400);
        }

        if(!count($employee))
        {
            return response()->json(['success' => false, 'errors' => "Employee not found"], 400);
        }else
        {
            if($employee->first_name != $request::get("first_name") && !empty($request::get("first_name")))
            {
                $employee->first_name = $request::get("first_name");
                $counter++;
            }
    
            
            if($employee->last_name != $request::get("last_name") && !empty($request::get("last_name")))
            {
                $employee->last_name = $request::get("last_name");
                $counter++;
            }
    
            if($employee->email != $request::get("email") && !empty($request::get("email")))
            {
                $employee->email = $request::get("email");
                $counter++;
            }
    
            if($employee->email != $request::get("salary") && !empty($request::get("salary")))
            {
                $employee->salary = $request::get("salary");
                $counter++;
            }
    
            if($employee->address_id != $request::get("address_id") && !empty($request::get("address_id")))
            {
                $employee->address_id = $request::get("address_id");
                $counter++;
            }
    
            if($employee->job_id != $request::get("job_id") && !empty($request::get("job_id")))
            {
                $employee->job_id = $request::get("job_id");
                $counter++;
            }
            if($counter != 0)
            {
                if($employee->save())
                {
                    return response()->json(['success' => true, 'errors' => "Successfully updated the database"], 200);
                }
                else
                {
                    return response()->json(['success' => false, 'errors' => "Unable to update the database "], 400);
                }
            }
            else
            {
                return response()->json(['success' => false, 'errors' => "No update needed."], 400);
            }
        }
    }

    public function sort()
    {
        
    }

    public function create()
    {

    }
}
