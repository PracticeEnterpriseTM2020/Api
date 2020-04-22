<?php

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
//Route::post('customers/showOne', 'customerController@show');
Route::post('customers/delete', 'customerController@destroy');
Route::middleware('APIToken')->group(function () {
    //test
  Route::post('customers', 'customerController@index');
  Route::post('customers/search', 'customerController@filter');
  Route::post('customers/showOne', 'customerController@show');
  Route::post('customers/change', 'customerController@update');
  Route::post('customers/logout','customerAuthController@logout');
  Route::post('employees/logout','employeeAuthController@logout');
});

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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});
