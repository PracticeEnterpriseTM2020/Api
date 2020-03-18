<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class employeeController extends Controller
{
    public function show_by_id(Employee $employee)
    {
        return $employee;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => "required|string",
            "last_name" => "required|string",
            "email" => "required|email|unique:employees,email",
            "password" => "required|string|confirmed",
            "salary" => "required|numeric|gte:0",
            "address_id" => "required|integer|exists:addresses,id",
            "job_id" => "required|integer|exists:jobs,id"
        ]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 409);

        $request->offsetSet("password", Hash::make($request->password));
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
        $validator = Validator::make($request->all(), [
            "first_name" => "string",
            "last_name" => "string",
            "email" => "email|unique:employees,email",
            "password" => "string|confirmed",
            "salary" => "numeric|gte:0",
            "address_id" => "integer|exists:addresses,id",
            "job_id" => "integer|exists:jobs,id"
        ]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 409);

        if ($request->password) $request->offsetSet("password", Hash::make($request->password));

        $employee->update($request->all());
        return response()->json($employee, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("employees");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "amount" => "integer|gt:0"
        ], ["in" => ":attribute must be one of the following types: :values"]);
        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        return Employee::where("first_name", "like", "%$search%")
            ->orWhere("last_name", "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:employees,email",
            "password" => "required"
        ]);

        if ($validator->fails()) return response()->json(["errors" => $validator->messages()], 400);

        $employee = Employee::where("email", "=", "maltenwerth@hotmail.com")->first();
        if (!Hash::check($request->password, $employee->password)) return response()->json(["error" => "Email and password do not match"], 401);

        return $employee;
    }
}
