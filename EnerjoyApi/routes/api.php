<?php

use App\Http\Controllers\MeterCustomerController;
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
Route::post('customers/new', 'customerController@store');
Route::post('customers/login', 'customerAuthController@Login');
Route::post('customers/activate', 'customerController@activate');
//Route::post('customers/showOne', 'customerController@show');
Route::middleware('APIToken')->group(function () {
  Route::post('customers/delete', 'customerController@destroy');
  Route::post('customers', 'customerController@index');
  Route::post('customers/search', 'customerController@filter');
  Route::post('customers/showOne', 'customerController@show');
  Route::post('customers/change', 'customerController@update');
  Route::post('customers/logout','customerAuthController@logout');
  Route::post('employees/logout','employeeAuthController@logout');
});

//Invoices
Route::middleware('APIToken')->group(function(){
  Route::post('invoices', 'invoiceController@index');
  Route::get('invoices/search', 'invoiceController@filter');
  Route::post('invoices/delete','invoiceController@destroy');
  Route::post('invoices/restore','invoiceController@restore');
  Route::post('invoices/create', 'invoiceController@store');
});


//Leveranciers
Route::get('/Leverancier/{manier?}/{zoek?}','SupplierController@ophalen');
Route::post('/aanmaak','SupplierController@store');
Route::post('/verwijder','SupplierController@softVerwijder');
Route::post('/herinstaleer', 'SupplierController@softHerinstaleer');
Route::post('/aanpas','SupplierController@aanpas');

//Meters
Route::get('meters/search', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');
Route::post('meters/edit', 'MetersController@edit');
Route::get('meters/delete','MetersController@softdelete');

//Meters&customers
Route::post('meters/connection/create', 'MeterCustomerController@store');
Route::post('meters/connection/delete', 'MeterCustomerController@softdelete');
Route::get('meters/connection/search', 'MeterCustomerController@search');

//Meter Data
Route::post('meters/usage/add', 'MeterDataController@store');

Route::get('employees','employeeController@filter');
Route::get('employees/{employee}','employeeController@show_by_id');
Route::post('employees','employeeController@store');
Route::delete('employees/{employee}','employeeController@destroy');
Route::put('employees/{employee}/restore','employeeController@restore');
Route::put('employees/{employee}','employeeController@update');
Route::post('employees/login','employeeAuthController@login');
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});