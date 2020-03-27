<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;

class employeeController extends Controller
{
    public function show_by_id(Employee $employee)
    {   
        return $employee;
    }

    public function store(Request $request)
    {
        $request->offsetSet('password', bcrypt($request->password));
        $employee = Employee::create($request->all());
        return response()->json($employee,201);
    }

    public function destroy(Request $request, Employee $employee)
    {
        $employee->delete();
        return response()->json(null,204);
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        return response()->json($employee,200);
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->all());
        return response()->json($employee,200);
    }

    public function filter(Request $request)
    {
        $sort = $request->input("sort","id"); // neem de sort uit de url of zet default als id
        $order = $request->input("order","asc");
        $search = $request->input("search","");

        try
        {
            return Employee::where("first_name", "like", "%$search%")
                ->orWhere("last_name", "like", "%$search%")
                ->orderBy($sort,$order)
                ->paginate(5);
        }
        catch(QueryException $e)
        {
            return response()->json(["message"=>"bad request"],400);
        }
    }
}