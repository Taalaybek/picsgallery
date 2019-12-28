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

/*** AUTHENTICATION ***/
Route::prefix('v1/auth')->group(function () {
    Route::namespace('API\V1\Auth')->group(function () {
        Route::post('login', 'LoginController@login')->name('auth.login');
        Route::post('register', 'RegisterController@register')->name('auth.register');
        Route::get('account-activation/{token}', 'AuthController@activateToken')->name('auth.account.activate');
        Route::post('token_refresh', 'AuthController@refreshToken')->name('auth.token_refresh');

        // PASSWORD RESETS
        Route::prefix('password')->group(function () {
            Route::post('reset', 'PasswordResetController@create')->name('auth.password.reset');
            Route::get('find/{token}', 'PasswordResetController@find')->name('auth.passport.find');
            Route::post('create/new', 'PasswordResetController@updatePassword')->name('auth.password.update');
        });

        Route::middleware(['auth:api', 'verified'])->group(function () {
           Route::get('user', 'AuthController@user')->name('auth.user');
           Route::get('logout', 'AuthController@logout')->name('auth.logout');
        });
    });
});

/*** ALBUMS ***/
Route::prefix('v1')->namespace('API\V1')->group(function () {
    /*** PROTECTED ***/
    Route::middleware(['auth:api', 'verified'])->group(function () {
        Route::post('albums', 'AlbumController@store')->name('albums.store');
    });

    /*** OPEN ALBUM ROUTES ***/
    Route::get('albums/{album}', 'AlbumController@show')->name('albums.show');
});