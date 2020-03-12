<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;

class employeeController extends Controller
{
    public function show_all()
    {
        return Employee::get();
    }

    public function show_by_id($employee_id)
    {   
        $select = Employee::where('id', 'like', '%' . $employee_id . '%')->get();
        return $select;
    }
}
