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
Route::get('Employees','employeeController@show_all');
Route::get('Employees/show_by_id/id={employee_id}','employeeController@show_by_id');
Route::post('Employees/create','employeeController@store');
Route::get('Employees/delete/email={email}','employeeController@destroy');
Route::get('Employees/restore/email={email}','employeeController@restore');
Route::post('Employees/update','employeeController@update');
Route::get('Employees/filter','employeeController@filter');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});