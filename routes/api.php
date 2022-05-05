<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', 'Api\AuthController@signup');
Route::get('register/verify/{client_id}/{token}', 'Api\AuthController@verifyEmail')->name('api.email.verify');
Route::post('login', 'Api\AuthController@login');
Route::get('logout', 'Api\AuthController@logout')->middleware('auth:sanctum');
Route::post('mobile/verify', 'Api\ClientController@submitMobileOtp');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    // Route::apiResource('projects', 'ProjectsApiController');
    // Route::apiResource('teams', 'TeamApiController');

    // mobile OTP
    // Route::post('/otp-mobile/send', 'Api\ClientController@sendMobileOtp');
    // Route::post('/otp-mobile/check', 'Api\ClientController@submitMobileOtp');
    // email OTP
    // Route::post('/otp-email/send', 'Api\ClientController@sendEmailOtp');
    // Route::post('/otp-email/check', 'Api\ClientController@submitEmailOtp');
    Route::post('/kyc/manual', 'Api\ClientController@kycManual');
    Route::post('/deposit', 'Api\ClientController@deposit');

    Route::get('/profile', 'Api\ClientController@showUser');
    Route::get('/portfolio', 'Api\ClientController@portfolio');
    Route::post('/pan', 'Api\ClientController@savePan');
    Route::post('/payout-process', 'Api\ClientController@processPayout');
    Route::post('/kyc/process', 'Api\ClientController@processKyc');
    
});

Route::post('/kyc/response', 'Api\ClientController@responseKyc');

//-------------------------------- Merchant(3rd party) -------------------------------------------//
////////////////// otp ///////////////////
// mobile OTP
Route::post('/otp/mobile/send', 'Api\MerchantController@sendMobileOtp')->name('securepay.sendMobileOtp');
Route::post('/otp/mobile/submit', 'Api\MerchantController@submitMobileOtp')->name('securepay.submitMobileOtp');
// email OTP
Route::post('/otp/email/send', 'Api\MerchantController@sendEmailOtp')->name('securepay.sendEmailOtp');
Route::post('/otp/email/submit', 'Api\MerchantController@submitEmailOtp')->name('securepay.submitEmailOtp');

////////////////// deposit ///////////////////
Route::post('/securepay/process', 'Api\MerchantController@index')->name('securepay.process');
Route::post('/securepay/validate', 'Api\MerchantController@validateVpa')->name('securepay.validate');
Route::get('/securepay/deposit', 'Api\MerchantController@deposit')->name('securepay.deposit');
Route::get('/securepay/upi', 'Api\MerchantController@getUpi')->name('securepay.upi');

///////////////// kyc /////////////////
Route::any('/securepay/kyc', 'Api\MerchantController@kycIndex')->name('securepay.kyc');
Route::post('/securepay/kyc/process', 'Api\MerchantController@kycProcess')->name('securepay.kyc.process');
Route::post('/securepay/kyc/response', 'Api\MerchantController@kycResponse')->name('securepay.kyc.response');
Route::post('/securepay/kyc/manual', 'Api\MerchantController@kycManual')->name('securepay.kyc.manual');
///////////////// payout /////////////////
Route::post('/securepay/payout', 'Api\MerchantController@payout')->name('securepay.payout');
Route::get('/securepay/payout/process', 'Api\MerchantController@addPayout')->name('securepay.payout.add');
Route::post('/securepay/pan/manual', 'Api\MerchantController@panManual')->name('securepay.pan.manual');
Route::any('/securepay/pan', 'Api\MerchantController@pan')->name('securepay.pan');
///////////////// external  /////////////////

//-------------------------------- Merchant(3rd party end) -------------------------------------------//
Route::post('/deposit/response', 'Api\ClientController@responseDeposit');
Route::post('/payout/response', 'Api\ClientController@responsePayout');