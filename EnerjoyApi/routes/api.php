<?php

use Illuminate\Http\Request;

<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
//customers
Route::post('customers/delete', 'customerController@destroy');
Route::post('customers/new', 'customerController@store');
Route::post('customers/login', 'customerController@verify');
Route::get('customers/{email}', 'customerController@show');
Route::get('customers', 'customerController@index');

//invoices
Route::get('invoices/{invoiceId}', 'invoiceController@show');

//Meters
Route::get('meters/{meter_id}', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');

=======
>>>>>>> Stashed changes

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
<<<<<<< Updated upstream
Route::fallback(function(){
    return response()->json(['message' => 'Page Not Found.'], 404);
});
=======
>>>>>>> Stashed changes
