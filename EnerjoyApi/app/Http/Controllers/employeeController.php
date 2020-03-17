<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use ErrorException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;

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
        return response()->json($employee, 201);
    }

    public function destroy(Request $request, Employee $employee)
    {
        $employee->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();
        return response()->json($employee, 200);
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->all());
        return response()->json($employee, 200);
    }

    public function filter(Request $request)
    {
        $sort = $request->input("sort", "id"); // neem de sort uit de url of zet default als id
        $order = $request->input("order", "asc");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        try {
            return Employee::where("first_name", "like", "%$search%")
                ->orWhere("last_name", "like", "%$search%")
                ->orderBy($sort, $order)
                ->paginate($amount);
        } catch (QueryException $e) {
            return response()->json(["message" => "Bad Request: sort does not exist"], 400);
        } catch (ErrorException $e) {
            return response()->json(["message" => "Bad Request: amount must be a numeric value"], 400);
        } catch (InvalidArgumentException $e) {
            return response()->json(["message" => "Bad Request: order must be asc or desc"], 400);
        }
    }
}
