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
Route::get('/shop','ShopController@shop')->name('shop');
Route::get('/auth','ShopController@authenticate')->name('authenticate');
Route::get('/install','ShopController@index');
Route::get('/thankspage/{url}',"ShopController@thankspage")->name("thankspage");

Route::get('/getprodect',"ShopController@getprodect")->name("getprodect");

Route::get('/customerget',"ShopController@customerget")->name("customerget");

Route::post('/thankspagedemo/{url}',"ShopController@thankspagedemo")->name("thankspagedemo");

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/','HomeController@homepage')->name('homepage');


Route::post('deletelabel','HomeController@deletelabel')->name('deletelabel');


Route::get('mail', 'HomeController@mail');


// Route::get('reateget', 'ApiController@reateget');
// Route::get('getaccountinfo', 'ApiController@getaccountinfo');
// Route::get('getpurchasestatus', 'ApiController@GetPurchaseStatus');
// Route::get('purchasepostage', 'ApiController@PurchasePostage');
// Route::get('cleanseaddress', 'ApiController@CleanseAddress');
// Route::get('createindicium', 'ApiController@createindicium');
// Route::get('cancelindicium', 'ApiController@CancelIndicium');
