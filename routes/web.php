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

Route::get('/breeds', 'ContentController@getContentByName')->middleware('throttle:15.1');
Route::get('/breeds/{id}', 'ContentController@getContentById')->middleware('throttle:15.1');

Route::get('/', function() {
    $data = json_encode(["message" => "HG Code Challenge"], JSON_PRETTY_PRINT);
    echo "<pre>";
    return $data;
});
