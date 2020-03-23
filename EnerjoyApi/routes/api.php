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

Route::post('customers/delete', 'customerController@destroy');
Route::post('customers/new', 'customerController@store');
Route::post('customers/login', 'customerController@verify');
Route::post('customers/change', 'customerController@update');
Route::post('customers/activate', 'customerController@activate');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');



Route::get('invoices/{invoiceId}', 'invoiceController@show');
Route::get('invoices', 'invoiceController@index');
Route::get('invoices/{invoiceId}', 'invoiceController@showSingle');
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
