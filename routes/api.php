<?php

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

Route::prefix('v1/auth')->group(function () {
    Route::namespace('API\V1\Auth')->group(function () {
        Route::post('login', 'LoginController@login')->name('auth.login');
        Route::post('register', 'RegisterController@register')->name('auth.register');
        Route::get('account-activation/{token}', 'AccountActivationController@activateToken')->name('auth.account.activate');

        Route::middleware(['auth:api', 'verified'])->group(function () {
           Route::get('user', 'AuthController@user')->name('auth.user');
        });
    });
});