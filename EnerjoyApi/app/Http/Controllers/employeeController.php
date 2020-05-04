<?php

namespace App\Http\Controllers;

use App\address;
use App\city;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use ErrorException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class employeeController extends Controller
{
    public function __construct()
    {
        $this->middleware("can:human-resources")->except(["login", "logout", "self", "refresh"]);
    }

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
            "phone" => "required|string",
            "ssn" => "required|string",
            "birthdate" => "required|date|before:" . date("Y/m/d"),
            "street" => "required|string",
            "number" => "required|string",
            "city" => "required|string",
            "postalcode" => "required|string",
            "country_id" => "required|integer|exists:countries,id",
            "job_id" => "required|integer|exists:jobs,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $city = city::firstOrCreate(["name" => $request->city, "postalcode" => $request->postalcode, "country_id" => $request->country_id]);
        $addr = address::firstOrCreate(["street" => $request->street, "number" => $request->number, "city_id" => $city->id]);

        $request->offsetSet("address_id", $addr->id);
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
            "first_name" => "required|string",
            "last_name" => "required|string",
            "password" => "nullable|string|confirmed",
            "salary" => "required|numeric|gte:0",
            "phone" => "required|string",
            "ssn" => "required|string",
            "birthdate" => "required|date|before:" . date("Y/m/d"),
            "street" => "required|string",
            "number" => "required|string",
            "city" => "required|string",
            "postalcode" => "required|string",
            "country_id" => "required|integer|exists:countries,id",
            "job_id" => "required|integer|exists:jobs,id"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $city = city::firstOrCreate(["name" => $request->city, "postalcode" => $request->postalcode, "country_id" => $request->country_id]);
        $addr = address::firstOrCreate(["street" => $request->street, "number" => $request->number, "city_id" => $city->id]);

        $request->offsetSet("address_id", $addr->id);
        if ($request->password) $employee->update($request->except(["email"]));
        else $employee->update($request->except(["email", "password"]));

        return response()->json($employee, 200);
    }

    public function filter(Request $request)
    {
        $cols = Schema::getColumnListing("employees");
        $validator = Validator::make($request->all(), [
            "sort" => Rule::in($cols),
            "order" => Rule::in(["asc", "desc"]),
            "amount" => "integer|gt:0"
        ]);
        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        $sort = $request->input("sort", "id");
        $order = $request->input("order", "asc");
        $search = $request->input("search", "");
        $amount = $request->input("amount", 5);

        $response = Employee::where("first_name", "like", "%$search%")
            ->orWhere("last_name", "like", "%$search%")
            ->orderBy($sort, $order)
            ->paginate($amount);

        return collect(["sort" => $sort, "order" => $order, "search" => $search])->merge($response);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:employees,email",
            "password" => "required"
        ]);

        if ($validator->fails()) return response()->json(["error" => $validator->messages()->all()], 400);

        if (!$token = Auth::attempt($request->only(["email", "password"]))) throw new AuthenticationException("errors.credentials");

        $employee = Employee::where("email", "=", $request->email)->first();
        $employee["token"] = $token;
        return $employee;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(["message" => "Successfully logged out."], 200);
    }

    public function self(Request $request)
    {
        $employee = $request->user();
        $employee["token"] = $request->bearerToken();
        return $employee;
    }

    public function refresh(Request $request)
    {
        $token = auth()->refresh();
        return response()->json(["token" => $token]);
    }
}
