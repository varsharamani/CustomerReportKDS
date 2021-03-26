<?php

use Illuminate\Support\Facades\Route;

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

/*Route::get('/', function () {
    return view('welcome');
});*/
//Route::get('/','GetController@index');

Route::get('/','GetController@getCustomer');
Route::post('/Filter','GetController@getCustomerFilter');
Route::get('/export/{type}', 'GetController@export');
Route::post('/getTotalTags', 'GetController@getTotalTags');
Route::post('/getMapData', 'GetController@getMapData');



/*Route::get('/', function () {
return view('welcome');

})->middleware(['auth.shopify'])->name('home');*/
