<?php

use App\Http\Controllers\employeeController;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//customers
Route::post('customers/delete', 'customerController@destroy');
Route::post('customers/login', 'customerController@verify');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');

//invoices
Route::get('invoices/{invoiceId}', 'invoiceController@show');

//Meters
Route::get('meters/{meter_id}', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');


//Employees
Route::get('employees', 'employeeController@filter');
Route::get('employees/{employee}', 'employeeController@show_by_id');
Route::post('employees', 'employeeController@store');
Route::delete('employees/{employee}', 'employeeController@destroy');
Route::put('employees/{employee}/restore', 'employeeController@restore');
Route::put('employees/{employee}', 'employeeController@update');

//Jobs
Route::get('jobs', 'jobController@filter');
Route::get('jobs/{job}', 'jobController@show_by_id');
Route::post('jobs', 'jobController@store');
Route::delete('jobs/{job}', 'jobController@destroy');
Route::put('jobs/{job}/restore', 'jobController@restore');
Route::put('jobs/{job}', 'jobController@update');


//Job Offers
Route::get("joboffers", "JobOfferController@filter");
Route::get("joboffers/{job_offer}", "JobOfferController@show");
Route::post("joboffers", "JobOfferController@store");
Route::put("joboffers/{job_offer}", "JobOfferController@update");
Route::delete("joboffers/{job_offer}", "JobOfferController@destroy");
Route::put("joboffers/{id}/restore", "JobOfferController@restore");

//Fleet
Route::get("fleet", "FleetController@filter");
Route::get("fleet/{fleet}", "FleetController@show");
Route::post("fleet", "FleetController@store");
Route::put("fleet/{fleet}", "FleetController@update");
Route::delete("fleet/{fleet}", "FleetController@destroy");
Route::put("fleet/{id}/restore", "FleetController@restore");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function () {
    return response()->json(['message' => 'Page Not Found.'], 404);
});
