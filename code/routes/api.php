<?php

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

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

// Route::get('register', 'API\RegisterController@Demotesting');
Route::post('reateget', 'API\AllapiController@reateget');
Route::get('getaccountinfo', 'API\AllapiController@getaccountinfo');
Route::post('getpurchasestatus', 'API\AllapiController@GetPurchaseStatus');
Route::post('purchasepostage', 'API\AllapiController@PurchasePostage');
Route::post('cleanseaddress', 'API\AllapiController@CleanseAddress');
Route::post('createindicium', 'API\AllapiController@createindicium');
Route::get('CancelIndicium/{id}', 'API\AllapiController@CancelIndicium');


Route::get('pdf/{id}', 'API\AllapiController@pdf');
Route::get('sendmail', 'API\AllapiController@sendmail');
Route::post('Demotesting22', 'API\RegisterController@Demotesting22');

Route::post('orderprocess','API\AllapiController@orderprocess');

Route::get('demoorderprocess','API\AllapiController@demoorder');

Route::get('mail2', 'API\AllapiController@mail2');

// Route::post('login', 'API\RegisterController@login');
   
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
