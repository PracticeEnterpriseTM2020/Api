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
Route::post('customers/login', 'customerController@verify');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');


//Invoices
Route::get('invoices/{invoiceId}', 'invoiceController@show');
Route::get('invoices', 'invoiceController@index');
Route::post('invoices/delete','invoiceController@destroy');
Route::get('invoices/{invoiceId}', 'invoiceController@showSingle');
Route::post('invoices/create', 'invoiceController@store');

//Meters
Route::get('meters/{meter_id}', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});