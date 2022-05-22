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








//-------------------------------- Admin -------------------------------------------//
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace'=>'Admin'], function() {
    Route::get('deposits', 'DepositController@index')->name('deposits');
    Route::post('deposits/data', 'DepositController@data')->name('deposits.data');
    Route::get('deposits/missed', 'DepositController@missedDeposit')->name('deposits.missed');
    Route::post('deposits/missed/data', 'DepositController@dataMissed')->name('deposits.missed.data');
    Route::get('deposits/manual', 'DepositController@mintManual')->name('deposits.mint.manual');

    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('success', 'DepositController@successIndex')->name('success');
    Route::get('pending', 'DepositController@pendingIndex')->name('pending');
    Route::get('activation', 'DepositController@activationIndex')->name('activation'); 
    Route::get('success/data', 'DepositController@successData')->name('success.data');
    Route::get('pending/data', 'DepositController@pendingData')->name('pending.data');
    Route::get('activation/data', 'DepositController@activationData')->name('activation.data');
    Route::get('activation/updateData/{id}', 'DepositController@activationUpdateData')->name('activation.updateData');

    Route::get('payouts', 'PayoutController@index')->name('payouts');
    Route::post('payouts/data', 'PayoutController@data')->name('payouts.data');
    Route::get('payouts/missed', 'PayoutController@missedPayout')->name('payouts.missed');
    Route::post('payouts/missed/data', 'PayoutController@dataMissed')->name('payouts.missed.data');


    Route::get('payouts/process', 'PayoutController@process')->name('payouts.process');
    Route::get('payouts/success', 'PayoutController@success')->name('payouts.success');
    Route::get('payouts/pending', 'PayoutController@pending')->name('payouts.pending');

    Route::get('users', 'UserController@index')->name('users');
    Route::post('users/data', 'UserController@data')->name('users.data');
    Route::get('users/approve', 'UserController@approve')->name('users.approve');

    Route::get('clients', 'ClientController@index')->name('clients');
    Route::post('clients/data', 'ClientController@data')->name('clients.data');
    Route::post('clients/store', 'ClientController@store')->name('clients.store');
});
//-------------------------------- Admin end -------------------------------------------//




//-------------------------------- Merchant enrollment -------------------------------------------//
Route::get('/', 'MerchantController@index')->name('home');
Route::post('merchant/add', 'MerchantController@addMerchant')->name('merchant.add');
Route::get('terms', 'MerchantController@terms')->name('terms');
//-------------------------------- Merchant enrollment end -------------------------------------------//

