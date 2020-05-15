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
Route::post('customers/delete', 'customerController@destroy');
Route::middleware('APIToken')->group(function () {
    Route::post('customers', 'customerController@index');
    Route::post('customers/search', 'customerController@filter');
    Route::post('customers/showOne', 'customerController@show');
    Route::post('customers/change', 'customerController@update');
    Route::post('customers/logout', 'customerAuthController@logout');
    Route::post('employees/logout', 'employeeAuthController@logout');
});

//Invoices
/*
Route::get('invoices', 'invoiceController@filter');
Route::post('invoices/delete','invoiceController@destroy');
Route::post('invoices/restore','invoiceController@restore');
Route::post('invoices/create', 'invoiceController@store');
*/

Route::prefix('invoices')->group(function () {
    Route::get('/', 'invoiceController@filter');
    Route::delete('/', 'invoiceController@destroy');
    Route::put('/restore', 'invoiceController@restore');
    Route::post('/', 'invoiceController@store');
});


//Leveranciers
Route::get('/Leverancier/{manier?}/{zoek?}', 'SupplierController@ophalen');
Route::post('/aanmaak', 'SupplierController@store');
Route::post('/verwijder', 'SupplierController@softVerwijder');
Route::post('/herinstaleer', 'SupplierController@softHerinstaleer');
Route::post('/aanpas', 'SupplierController@aanpas');

//Meters
Route::get('meters/search', 'MetersController@show');
Route::post('meters/create', 'MetersController@store');
Route::post('meters/edit', 'MetersController@edit');
Route::get('meters/delete', 'MetersController@softdelete');

//Meters&customers
Route::post('meters/connection/create', 'MeterCustomerController@store');
Route::post('meters/connection/delete', 'MeterCustomerController@softdelete');
Route::get('meters/connection/search', 'MeterCustomerController@search');

//Meter Data
Route::post('meters/usage/add', 'MeterDataController@store');

//Employees
Route::prefix("employees")->group(function () {
    Route::post('/login', 'employeeController@login');
    Route::get('/refresh', 'employeeController@refresh');
    Route::middleware("auth")->group(function () {
        Route::get('/', 'employeeController@filter');
        Route::get('/self', 'employeeController@self');
        Route::get('/{employee}', 'employeeController@show_by_id');
        Route::post('/', 'employeeController@store');
        Route::delete('/logout', 'employeeController@logout');
        Route::delete('/{employee}', 'employeeController@destroy');
        Route::put('/{employee}/restore', 'employeeController@restore');
        Route::put('/{employee}', 'employeeController@update');
    });
});

//Jobs
Route::prefix('jobs')->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get('/', 'jobController@filter');
        Route::get('/{job}', 'jobController@show_by_id');
        Route::post('/', 'jobController@store');
        Route::delete('/{job}', 'jobController@destroy');
        Route::put('/{job}/restore', 'jobController@restore');
        Route::put('/{job}', 'jobController@update');
    });
});


//Job Offers
Route::prefix("joboffers")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "JobOfferController@filter");
        Route::get("/{job_offer}", "JobOfferController@show");
        Route::post("/", "JobOfferController@store");
        Route::put("/{job_offer}", "JobOfferController@update");
        Route::delete("/{job_offer}", "JobOfferController@destroy");
        Route::put("/{id}/restore", "JobOfferController@restore");
    });
});

//Fleet
Route::prefix("fleet")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "FleetController@filter");
        Route::get("/{fleet}", "FleetController@show");
        Route::post("/", "FleetController@store");
        Route::put("/{fleet}", "FleetController@update");
        Route::delete("/{fleet}", "FleetController@destroy");
        Route::put("/{id}/restore", "FleetController@restore");
    });
});

//Countries
Route::prefix("countries")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "CountryController@filter");
        Route::get("/{country}", "CountryController@show");
        Route::post("/", "CountryController@store");
        Route::put("/{country}", "CountryController@update");
        Route::delete("/{country}", "CountryController@destroy");
        Route::put("/{id}/restore", "CountryController@restore");
    });
});

//Articles
Route::prefix("articles")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "ArticleController@filter");
        Route::get("/{article}", "ArticleController@getById");
        Route::post("/", "ArticleController@create");
        Route::put("/{article}", "ArticleController@update");
        Route::delete("/{article}", "ArticleController@delete");
        Route::put("/{id}/restore", "ArticleController@restore");
    });
});

//Conversations
Route::prefix("conversations")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "ConversationController@filter");
        Route::get("/{conversation}", "ConversationController@getById");
        Route::post("/", "ConversationController@create");
        Route::delete("/{conversation}", "ConversationController@delete");
        Route::put("/{id}/restore", "ConversationController@restore");
        Route::put("/{conversation}", "ConversationController@update");
    });
});

//Messages
Route::prefix("messages")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/", "MessageController@filter");
        Route::post("/", "MessageController@create");
        Route::delete("/{message}", "MessageController@delete");
        Route::put("/{id}/restore", "MessageController@restore");
        Route::put("/{message}", "MessageController@update");
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function () {
    return response()->json(['message' => 'Page Not Found.'], 404);
});
