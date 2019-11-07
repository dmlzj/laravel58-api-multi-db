<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function () {
	Route::group( [ 'prefix' => 'users' ], function () {
        Route::post('login', 'UserController@userLogin');
        // Route::post('admin/login', 'UserController@adminLogin')->name('admin.login');
        Route::post('admin/login', 'UserController@adminLogin');
        Route::post('logout', 'UserController@userLogout');
        Route::post('admin/logout', 'UserController@adminLogout');
        Route::get('admin/info', 'UserController@adminInfo');
        Route::post('regist', 'UserController@store');
    });
    Route::group(['prefix' => 'permissions', 'middleware' => ['jwt.role:admin', 'admin.auth']], function () {
        // Route::get('test', 'TestController@test');
        Route::get('/', 'PermissionController@index')->name('permission.index');
        Route::post('add', 'PermissionController@store')->name('permission.add');
        Route::post('update/{id}', 'PermissionController@update')->name('permission.update');
    });
	// Route::group( [ 'prefix' => 'users'  , 'middleware'=>['jwt.role:user','api.auth'] ], function () {
    Route::group( [ 'prefix' => 'users' ], function () {
        Route::get('/', 'UserController@index');
        Route::get('/{id}', 'UserController@show');
        Route::post('logout', 'UserController@destroy');
    });
});
