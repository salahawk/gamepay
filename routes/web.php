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

//-------------------------------- Direct -------------------------------------------//
// Authentication
// Route::get('/', 'AuthController@index')->name('index');
// Route::post('/user/signup', 'AuthController@signup')->name('signup');
// Route::post('/user/login', 'AuthController@login')->name('login');
// Route::get('/user/logout', 'AuthController@logout')->name('logout');
// Route::get('/user/verify/{token}', 'AuthController@verifyEmail')->name('verify'); 

// Route::group(['middleware' => 'auth'], function() {
    // Route::get('/user/check', 'DirectUserController@checkUser')->name('user-check');

    // // mobile OTP
    // Route::post('/otp/mobile/send', 'DirectUserController@sendMobileOtp')->name('send-mobile-otp');
    // Route::post('/otp/mobile/submit', 'DirectUserController@submitMobileOtp')->name('submit-mobile-otp');
    // // email OTP
    // Route::post('/otp/email/send', 'DirectUserController@sendEmailOtp')->name('send-email-otp');
    // Route::post('/otp/email/submit', 'DirectUserController@submitEmailOtp')->name('submit-email-otp');
    // // kyc
    // Route::any('/kyc', 'DirectUserController@kycIndex')->name('kyc');
    // Route::post('/kyc/process', 'DirectUserController@kycProcess')->name('kyc-process');
    // Route::post('/kyc/response', 'DirectUserController@kycResponse')->name('kyc-response');
    // Route::post('/kyc/manual', 'DirectUserController@kycManual')->name('kyc-manual');

    // // exchange-buy
    // Route::post('/vpa/validate', 'DirectUserController@validateVpa')->name('validate-vpa');
    // Route::get('/deposit/send', 'DirectUserController@sendDeposit')->name('send-deposit');
    // Route::post('/upi/response', 'DirectUserController@upiResponse')->name('upi-response');
    

    // // exchange-sell
    // Route::post('/payout/process', 'DirectUserController@processPayout')->name('process-payout');

    // ////////////////////   pages   /////////////////////////////////
    // Route::get('/privacy', 'AuthController@privacy')->name('privacy');
    // Route::get('/terms', 'AuthController@terms')->name('terms');
    // Route::get('/contact', 'AuthController@contact')->name('contact');
    // Route::get('/refund-policy', 'AuthController@refundPolicy')->name('refund-policy');
    // Route::get('/exchange', 'DirectUserController@index')->name('exchange');
    // Route::get('/portfolio', 'DirectUserController@portfolio')->name('portfolio');
    // Route::get('/profile', 'DirectUserController@profile')->name('profile');
    // Route::get('/profile/edit', 'DirectUserController@profileEdit')->name('profile.edit');
    // Route::get('/sell', 'DirectUserController@sell')->name('sell');

    // Route::post('/cashlesso/send', 'DirectUserController@sendCashlesso')->name('send-cashlesso');
    // Route::post('/cashlesso/response', 'DirectUserController@responseCashlesso')->name('response-cashlesso');
    //-------------------------------- Direct end -------------------------------------------//
// });








//-------------------------------- Super Admin -------------------------------------------//
Route::get('admin', 'Admin\UserController@index')->name('admin');
Route::post('admin/login', 'Admin\UserController@login')->name('admin.login');
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace'=>'Admin'], function() {
    Route::get('deposits', 'DepositController@index')->name('deposits');
    Route::post('deposits/data', 'DepositController@data')->name('deposits.data');
    Route::get('deposits/missed', 'DepositController@missed')->name('deposits.missed');
    Route::post('deposits/missed/data', 'DepositController@missedData')->name('deposits.missed.data');
    Route::get('deposits/release', 'DepositController@release')->name('deposits.release');
    Route::get('deposits/reject', 'DepositController@reject')->name('deposits.reject');

    Route::get('withdrawals', 'WithdrawalController@index')->name('withdrawals');
    Route::post('withdrawals/data', 'WithdrawalController@data')->name('withdrawals.data');
    Route::get('withdrawals/release', 'WithdrawalController@release')->name('withdrawals.release');
    Route::get('withdrawals/missed', 'WithdrawalController@missed')->name('withdrawals.missed');
    Route::post('withdrawals/missed/data', 'WithdrawalController@missedData')->name('withdrawals.missed.data');

    Route::get('users', 'UserController@users')->name('users');
    Route::post('users/data', 'UserController@data')->name('users.data');
    Route::get('users/kyc', 'UserController@kyc')->name('users.kyc');
    Route::post('users/kyc/kycData', 'UserController@kycData')->name('users.kycData');
    Route::get('users/kyc/verify', 'UserController@kycVerify')->name('users.kyc.verify');
    Route::get('users/kyc/cancel', 'UserController@kycCancel')->name('users.kyc.cancel');
    Route::post('users/kyc/remarks', 'UserController@remarks')->name('users.remarks');
    Route::get('users/pan/verify', 'UserController@panVerify')->name('users.pan.verify');
    Route::get('users/pan/cancel', 'UserController@panCancel')->name('users.pan.cancel');
});
//-------------------------------- Super Admin end -------------------------------------------//




//-------------------------------- Merchant enrollment -------------------------------------------//
Route::get('/', 'MerchantController@index')->name('home');
Route::post('merchant/add', 'MerchantController@addMerchant')->name('merchant.add');
Route::get('terms', 'MerchantController@terms')->name('terms');
//-------------------------------- Merchant enrollment end -------------------------------------------//


//-------------------------------- Merchant Admin -------------------------------------------//
Route::post('merchant/admin/login', 'AdminMerchant\UserController@login')->name('admin-merchant.login');
Route::get('merchant/admin', 'AdminMerchant\UserController@index')->name('admin-merchant.home');
Route::group(['prefix' => 'merchant/admin', 'as' => 'admin-merchant.', 'middleware' => 'auth.gamepay', 'namespace'=>'AdminMerchant'], function() {
    Route::get('deposits', 'DepositController@index')->name('deposits');
    Route::post('deposits/data', 'DepositController@data')->name('deposits.data');
    Route::get('deposit-guide', 'DepositController@guide')->name('deposits.guide');
    Route::get('withdrawals', 'WithdrawalController@index')->name('withdrawals');
    Route::post('withdrawals/data', 'WithdrawalController@data')->name('withdrawals.data');
    Route::get('withdrawal-guide', 'WithdrawalController@guide')->name('withdrawals.guide');
    Route::get('users', 'UserController@users')->name('users');
    Route::get('fee', 'UserController@fee')->name('users.fee');
    Route::post('users/data', 'UserController@data')->name('users.data');
    Route::get('rolling', 'UserController@rolling')->name('rolling');
    Route::get('swap', 'SwapController@index')->name('swap');
    Route::get('profile', 'UserController@profile')->name('users.profile');
    Route::post('profile/changePassworrd', 'UserController@changePassword')->name('users.changePassword');
    Route::get('logout', 'UserController@logout')->name('logout');
});
//-------------------------------- Merchant Admin -------------------------------------------//
