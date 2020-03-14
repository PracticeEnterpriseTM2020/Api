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
Route::get('employees','employeeController@show_all');
Route::get('employees/show_by_id/id={employee_id}','employeeController@show_by_id');
Route::post('employees/create','employeeController@store');
Route::get('employees/delete/email={email}','employeeController@destroy');
Route::get('employees/restore/email={email}','employeeController@restore');
Route::post('employees/update','employeeController@update');
Route::get('employees/filter','employeeController@filter');

//Jobs
Route::get('jobs','jobController@show_all');
Route::get('jobs/show_by_id/id={job_id}','jobController@show_by_id');
Route::post('jobs/create','jobController@store');
Route::get('jobs/delete/title={title}','jobController@destroy');
Route::get('jobs/restore/title={title}','jobController@restore');
Route::post('jobs/update','jobController@update');
Route::get('jobs/filter','jobController@filter');



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});