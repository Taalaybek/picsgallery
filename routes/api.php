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

/*** USERS ***/
Route::prefix('v1/users')->namespace('API\V1')->group(function () {
	Route::get('', 'UserController@index')->name('users.index');
	Route::get('{user}', 'UserController@show')->name('users.show');
	Route::get('{user}/relationship/albums', 'UserRelationshipsController@albums')->name('users.relationship.albums');
	Route::get('{user}/relationship/photos', 'UserRelationshipsController@photos')->name('users.relationship.photos');
});

/*** ALBUMS ***/
Route::prefix('v1/albums')->namespace('API\V1')->group(function () {
	/*** PROTECTED ***/
	Route::middleware(['auth:api', 'verified'])->group(function () {
		Route::post('', 'AlbumController@store')->name('albums.store');
		Route::delete('{album}', 'AlbumController@destroy')->name('albums.destroy');
		Route::patch('{album}', 'AlbumController@update')->name('albums.update');
		Route::get('authenticatedUserAlbums', 'AlbumRelationshipsController@authenticatedUserAlbums')->name('albums.creatorAlbums');
	});

	/*** UNPROTECTED ALBUM ROUTES ***/
	Route::get('', 'AlbumController@index')->name('albums.index');
	Route::get('{album}', 'AlbumController@show')->name('albums.show');
	// RELATIONSHIPS RESOURCES ROUTES
	Route::get('relationship/user/{user}', 'AlbumRelationshipsController@userAlbums')->name('albums.userAlbums');
	Route::get('{album}/relationship/photos', 'AlbumRelationshipsController@photos')->name('albums.relationship.photos');
	Route::get('{album}/relationship/oldestPhoto', 'AlbumRelationshipsController@oldestPhoto')->name('albums.relationship.oldestPhoto');
	Route::get('{album}/relationship/user/{user}', 'AlbumRelationshipsController@withUser')->name('albums.relationship.withUser');
});

/*** PHOTOS ***/
Route::prefix('v1/photos')->namespace('API\V1')->group(function () {
  /*** PROTECTED ***/
  Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('{album}', 'PhotoController@store')->name('photos.store');
    Route::delete('{photo}', 'PhotoController@destroy')->name('photos.destroy');
    Route::put('{photo}', 'PhotoController@update')->name('photos.update');
		Route::post('addTemp/new', 'PhotoController@addTempPhoto')->name('photos.store.temporary');
  });

	/*** UNPROTECTED ROUTES ***/
	Route::get('', 'PhotoController@index')->name('photos.index');
	Route::get('{photo}', 'PhotoController@show')->name('photos.show');
	// RELATIONSHIPS RESOURCES ROUTES
	Route::get('{photo}/relationship/album', 'PhotoRelationshipsController@album')->name('photos.relationships.album');
	Route::get('{photo}/relationship/creator', 'PhotoRelationshipsController@creator')->name('photos.relationships.creator');
});

/*** UNCATEGORIZED API ROUTES ***/
Route::prefix('v1/common')->namespace('API\V1')->group(function () {
	/*** UNPROTECTED ROUTES ***/
	Route::post('checkEmail', 'UserController@checkEmail')->name('common.checkEmail');
});
