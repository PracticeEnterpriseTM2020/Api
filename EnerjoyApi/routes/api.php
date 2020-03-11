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
Route::get('customers/{email}/login', 'customerController@showLogin');
Route::get('customers/login', 'customerController@indexLogin');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');
Route::get('invoices/{invoiceId}', 'invoiceController@show');

//Meters
Route::get('meters/search', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');
Route::get('meters/delete','MetersController@SoftDelete');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});*/