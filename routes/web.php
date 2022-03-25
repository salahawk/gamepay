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
Route::post('/user/check', 'MerchantController@checkUser')->name('check-user');
Route::get('/otp/select', 'MerchantController@selectOtp')->name('select-otp');

Route::post('/otp/mobile/send', 'MerchantController@sendMobileOtp')->name('send-mobile-otp');
Route::post('/otp/mobile/submit', 'MerchantController@submitMobileOtp')->name('submit-mobile-otp');

Route::post('/otp/email/send', 'MerchantController@sendEmailOtp')->name('send-email-otp');
Route::post('/otp/email/submit', 'MerchantController@submitEmailOtp')->name('submit-email-otp');

Route::post('/vpa/validate', 'MerchantController@validateVpa')->name('validate-vpa');

Route::get('/deposit/send', 'MerchantController@sendDeposit')->name('send-deposit');
Route::post('/upi/response', 'MerchantController@upiResponse')->name('upi-response');

Route::post('/mint/manual', 'MerchantController@mintManual')->name('mint-manual');

Route::get('/kyc', 'MerchantController@kyc')->name('kyc');




Route::post('/cashlesso/send', 'MerchantController@sendCashlesso')->name('send-cashlesso');
Route::post('/cashlesso/response', 'MerchantController@responseCashlesso')->name('response-cashlesso');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace'=>'Admin'], function() {
    Route::get('deposits', 'DepositController@index')->name('deposits');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('deposits/data', 'DepositController@data')->name('deposits.data');
});

