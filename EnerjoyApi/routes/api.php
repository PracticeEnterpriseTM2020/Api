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
Route::post('customers/login', 'customerController@verify');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');
Route::get('invoices/{invoiceId}', 'invoiceController@show');

//Meters
Route::get('meters/{meter_id}', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');

//Leveranciers
Route::get('/Leverancier/{manier}/{zoek}','BedrijfController@ophalen');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});*/