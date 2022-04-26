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
Route::get('register/verify/{psp_id}/{token}', 'Api\AuthController@verifyEmail')->name('api.email.verify');
Route::post('login', 'Api\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // Route::apiResource('projects', 'ProjectsApiController');
    // Route::apiResource('teams', 'TeamApiController');

    // mobile OTP
    Route::post('/otp-mobile/send', 'Api\ClientController@sendMobileOtp');
    Route::post('/otp-mobile/check', 'Api\ClientController@submitMobileOtp');
    // email OTP
    // Route::post('/otp-email/send', 'Api\ClientController@sendEmailOtp');
    // Route::post('/otp-email/check', 'Api\ClientController@submitEmailOtp');
    Route::post('/kyc/manual', 'Api\ClientController@kycManual');
    Route::post('/deposit', 'Api\ClientController@deposit');

    Route::get('/user', 'Api\ClientController@showUser');
    Route::get('/portfolio', 'Api\ClientController@portfolio');
    Route::post('/pan', 'Api\ClientController@savePan');
    Route::post('/payout-process', 'Api\ClientController@processPayout');
    Route::post('/kyc/process', 'Api\ClientController@processKyc');
    
});

Route::post('/kyc/response', 'Api\ClientController@responseKyc');
Route::post('/upi/response', 'Api\ClientController@responseUpi');