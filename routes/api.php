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

use App\Http\Controllers\Crm\EmailVerificationController;

Route::get('_healthcheck', function () {
    return 'OK';
});

Route::get('_phpinfo', function () {
    return phpinfo();
});



//authorized routes users
Route::group(['prefix' => 'auth', 'middleware' => ['guest']], function () {
    Route::post('login', 'Auth\AuthController@login');
//    Route::post('forgot-password', 'Auth\AuthController@forgotPassword');
    Route::post('change-password', 'Auth\AuthController@changePassword');
    Route::post('register', 'Auth\AuthController@register');
    Route::post('recovery', 'Auth\AuthController@recover');
    Route::get('verify/{verify_code}', 'Auth\AuthController@verifyEmail');
});

//authorized routes crm
Route::group(['prefix' => 'crm', 'middleware' => ['guest'], 'namespace' => 'Crm'], function () {
    Route::post('auth/login', 'AuthController@login');
});

Route::group(['prefix' => 'crm', 'namespace' => 'Crm', 'middleware' => ['auth.jwt:admin']], function () {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/refresh', 'AuthController@refresh');
});

Route::group(['prefix' => 'crm', 'namespace' => 'Crm', 'middleware' => ['auth.jwt:admin']], function () {

    Route::post('resource-data','ResourceDataController@getData');

    Route::get('resource-data/{value_id}','ResourceDataController@getOneData');

    Route::get('resource-data-list','ResourceDataController@getList');

});
