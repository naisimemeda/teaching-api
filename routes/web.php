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

Route::get('line', 'AuthController@line');
Route::any('line/callback', 'AuthController@callback');
Route::any('send', 'AuthController@send');

Route::post('admin/notification', 'NotificationController@sendNotification');
