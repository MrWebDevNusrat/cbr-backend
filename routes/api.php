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


Route::group(['prefix' => 'user'],function (){
    Route::get('guest-publisher-resources','User\ResourceController@index');
    Route::get('guest-publisher-resources/{id}','User\ResourceController@show');
    Route::get('guest-publisher-resources/{id}/{file_name}/{file_extension}','User\ResourceController@downloadFile');
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

Route::group(['prefix' => 'crm', 'namespace' => 'Crm'], function () {
    Route::get('i18n/lists', 'I18nController@lists');
    Route::get('languages', 'LanguageController@index');
});


Route::group(['prefix' => 'crm', 'namespace' => 'Crm', 'middleware' => ['auth:api']], function () {

    Route::get('posts/lists', 'PostController@lists');
    Route::get('resource-categories/lists', 'ResourceCategoryController@lists');
    Route::get('resource-types/lists', 'ResourceTypeController@lists');
    Route::get('resource-fields/lists', 'ResourceFieldController@lists');
    Route::get('resource-modifiers/lists', 'ResourceModifierController@lists');
    Route::get('landing-position/lists', 'LandingPositionController@lists');
    Route::get('resource-languages/lists', 'ResourceLanguageController@lists');
    Route::get('journal-types/lists', 'JournalTypeController@lists');
    Route::get('journals/lists', 'JournalController@lists');
    Route::get('countries/lists', 'CountryController@lists');
    Route::get('regions/lists', 'RegionController@lists');
    Route::get('admins/lists', 'AdminController@lists');
    Route::get('permissions/lists', 'PermissionController@lists');
    Route::get('universities/lists', 'UniversityController@lists');
    Route::get('publisher-resources/lists', 'PublisherResourceController@lists');

    //language module
    Route::apiResource('i18n', 'I18nController');
});

Route::group(['prefix' => 'crm', 'namespace' => 'Crm', 'middleware' => ['auth.jwt:admin']], function () {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/refresh', 'AuthController@refresh');
});

Route::group(['prefix' => 'crm', 'namespace' => 'Crm', 'middleware' => ['auth.jwt:admin']], function () {

    //posts module
    Route::apiResource('posts', 'PostController');
    // Ресурс тоифаси модули
    Route::apiResource('resource-categories', 'ResourceCategoryController');
    // Ресурс тури модули
    Route::apiResource('resource-types', 'ResourceTypeController');

    Route::get('resource-data','ResourceTypeController@getData');

    Route::get('resource-data/{value_id}','ResourceTypeController@getOneData');
    // Ресурс соҳаси модули
    Route::apiResource('resource-fields', 'ResourceFieldController');
    // Ресурс очиқлиги модули
    Route::apiResource('resource-modifiers', 'ResourceModifierController');
    // Ресурс соҳаси модули
    Route::apiResource('landing-position', 'LandingPositionController');
    Route::put('publisher-resources/update-landing-position/{id}', 'PublisherResourceController@updateLandingPosition');
    //  Ресурс тили модули
    Route::apiResource('resource-languages', 'ResourceLanguageController');
    //  Журнал типи модули
    Route::apiResource('journal-types', 'JournalTypeController');
    //  Журнал модули
    Route::apiResource('journals', 'JournalController');
    //давлатлар модули
    Route::apiResource('countries', 'CountryController');
    //ҳудудлар модули
    Route::apiResource('regions', 'RegionController');
    Route::apiResource('admins', 'AdminController');
    ///profile
    Route::get('profile', 'ProfileController@view');
    Route::post('profile/change-password', 'ProfileController@changePassword');
    Route::post('profile/photo', 'ProfileController@photo');

    //config module
    Route::get('config', 'ConfigController@index');
    Route::put('config/{id}', 'ConfigController@update');
    Route::get('config/{id}', 'ConfigController@show');


    //role permission module

    Route::get('permission-groups/lists', 'PermissionGroupController@lists');
    Route::apiResource('permission-groups', 'PermissionGroupController');

    Route::get('backup-database', 'BackupController@index');
    Route::post('backup-store', 'BackupController@store');
    Route::delete('backup-delete/{path}', 'BackupController@destroy');

    // users
    Route::put('users/update-role/{id}', 'UserController@updateRole');
    Route::get('users/lists', 'UserController@lists');
    Route::apiResource('users', 'UserController');

    //university structure
    Route::apiResource('universities', 'UniversityController');
    Route::apiResource('publisher-resources', 'PublisherResourceController');

});

Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth.jwt:user']], function ($router) {
    Route::get('publisher-resources/lists', 'PublisherResourceController@lists');
    Route::apiResource('publisher-resources', 'PublisherResourceController');
});


Route::group(['prefix' => 'auth', 'middleware' => ['auth.jwt:user']], function () {

    Route::get('logout', 'Auth\AuthController@logout');
    Route::get('refresh', 'Auth\AuthController@refresh');

    Route::get('get-permission-groups/{id}', 'Common\ProfileController@getPermissionGroup');
    Route::get('profile/show', 'Common\ProfileController@show');
    Route::post('profile/update-profile', 'Common\ProfileController@updateProfile');
    Route::post('profile/change-password', 'Common\ProfileController@changePassword');
    Route::post('profile/photo', 'Common\ProfileController@photo');

});

//resources uchun route
Route::group(['prefix' => 'resources', 'middleware' => ['guest']], function () {
    // File save start  ---
    //Image uploads
    Route::post('storeImage', 'Resources\FileController@storeImage');
    //Test file upload
    Route::post('storeTest', 'Resources\FileController@storeTestFile');
    //File uploads
    Route::post('storeFile', 'Resources\FileController@storeFile');
    //Image uploads
    Route::post('storeImageEditor', 'Resources\FileController@storeImageEditor');
    //File uploads
    Route::post('storeFileEditor', 'Resources\FileController@storeFileEditor');
    //-----
    //File download
    Route::get('download/{uuid}', 'Resources\FileController@download');
    //original file url
    Route::get('mediaUrl/{uuid}', 'Resources\FileController@mediaUrl');
    Route::get('FileUrl/{uuid}', 'Resources\FileController@FileUrl');
    //Show images with request: width and height
    Route::get('showImage/{uuid}', 'Resources\FileController@showImage');
    //Move folder images with resize images
    Route::get('moveFolderImage/{uuid}', 'Resources\FileController@moveFolderImage');
    Route::get('moveFolderFile/{uuid}', 'Resources\FileController@moveFolderFile');
});
