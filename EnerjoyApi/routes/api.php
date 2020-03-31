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


//Customers

Route::post('customers/delete', 'customerController@destroy');
Route::post('customers/new', 'customerController@store');
Route::post('customers/login', 'customerController@verify');
Route::post('customers/change', 'customerController@update');
Route::post('customers/activate', 'customerController@activate');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');


//Invoices
Route::get('invoices', 'invoiceController@filter');
Route::post('invoices/delete','invoiceController@destroy');
Route::post('invoices/restore','invoiceController@restore');
Route::post('invoices/create', 'invoiceController@store');




//Meters
Route::get('meters/search', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');
Route::post('meters/edit', 'MetersController@edit');
Route::get('meters/delete','MetersController@softdelete');

//Employees
Route::get('employees','employeeController@filter')->middleware('cors');
Route::get('employees/{employee}','employeeController@show_by_id');
Route::post('employees','employeeController@store');
Route::delete('employees/{employee}','employeeController@destroy');
Route::put('employees/{employee}/restore','employeeController@restore');
Route::put('employees/{employee}','employeeController@update');

//Jobs
Route::get('jobs','jobController@filter');
Route::get('jobs/{job}','jobController@show_by_id');
Route::post('jobs','jobController@store');
Route::delete('jobs/{job}','jobController@destroy');
Route::put('jobs/{job}/restore','jobController@restore');
Route::put('jobs/{job}','jobController@update');



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});
