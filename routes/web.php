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

Route::get('/', 'MerchantController@index')->name('home');
Route::post('/cashlesso/send', 'MerchantController@sendCashlesso')->name('send-cashlesso');
Route::post('/cashlesso/response', 'MerchantController@responseCashlesso')->name('response-cashlesso');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace'=>'Admin'], function() {
    Route::get('deposits', 'DepositController@index')->name('deposits');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('success', 'SuccessController@index')->name('success');
    Route::get('pending', 'PendingController@index')->name('pending');

    Route::get('deposits/data', 'DepositController@data')->name('deposits.data');
    Route::get('success/data', 'SuccessController@data')->name('success.data');
    Route::get('pending/data', 'PendingController@data')->name('pending.data');
});

