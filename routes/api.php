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
        Route::post('admin/login', 'UserController@adminLogin');
        Route::post('logout', 'UserController@userLogout');
        Route::post('admin/logout', 'UserController@adminLogout');
        Route::get('admin/info', 'UserController@adminInfo');
        Route::post('regist', 'UserController@store');
    });
    Route::group(['preffix' => 'test', 'middleware' => ['jwt.role:admin', 'admin.auth']], function () {
        Route::get('test', 'TestController@test');
    });
	// Route::group( [ 'prefix' => 'users'  , 'middleware'=>['jwt.role:user','api.auth'] ], function () {
    Route::group( [ 'prefix' => 'users' ], function () {
        Route::get('/', 'UserController@index');
        Route::get('/{id}', 'UserController@show');
        Route::post('logout', 'UserController@destroy');
    });
});
