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
Route::get('register/verify/{token}', 'Api\AuthController@verifyEmail');
Route::post('login', 'Api\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // Route::apiResource('projects', 'ProjectsApiController');
    // Route::apiResource('teams', 'TeamApiController');

    // mobile OTP
    Route::post('/otp-mobile/send', 'Api\BuyController@sendMobileOtp');
    Route::post('/otp-mobile/check', 'Api\BuyController@submitMobileOtp');
    // email OTP
    // Route::post('/otp-email/send', 'Api\BuyController@sendEmailOtp');
    // Route::post('/otp-email/check', 'Api\BuyController@submitEmailOtp');
    Route::post('/kyc/manual', 'Api\BuyController@kycManual');
    Route::post('/deposit', 'Api\BuyController@deposit');

    Route::get('/user', 'Api\BuyController@showUser');
    Route::get('/portfolio', 'Api\BuyController@portfolio');

    
    
});