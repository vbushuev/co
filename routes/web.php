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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');


Route::get('/create', 'CoController@create');
Route::get('/update', 'CoController@update');
Route::get('/pay', 'CoController@pay');
Route::get('/{id}', 'CoController@index');
Route::get('/user/{id}', 'UserController@index');

Route::post('/payoutresponse', 'CoController@payoutresponse');
