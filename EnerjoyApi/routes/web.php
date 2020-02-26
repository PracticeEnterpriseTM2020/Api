<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\MetersController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/meters', 'MetersController@index');
Route::post('/meters', 'MetersController@store');
Route::get('/meters/create', 'MetersController@create');
Route::get('/meters/{meter_id}', 'MetersController@show');
