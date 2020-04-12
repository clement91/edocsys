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

Auth::routes(['verify' => true]);

//Home controller
Route::get('/home', 'HomeController@index')->name('home');

//Form controller
Route::get('/Form/index', 'FormController@index');
Route::get('/Form/GetForms', 'FormController@GetForms');
Route::post('/Form/update', 'FormController@update');
Route::post('/Form/upload', 'FormController@upload');

//Auth form controller
Route::get('form/{id?}', 'FormAuthController@formauth');
