<?php

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

/***********************************************************************************************************************
 *                                              Rutas Privadas                                                         *
 ***********************************************************************************************************************/

Route::group(['middleware' => ['auth', 'access']], function () {
    Route::resource('roles', 'Role\RoleController')->except('create', 'edit');

    Route::resource('permissions', 'Permission\PermissionController')->except('create', 'edit');;
    Route::delete('permissions', 'Permission\PermissionController@clean')->name('permissions.clean');

    Route::resource('roles.permissions', 'Role\RolePermissionController')->only('index', 'store');
    
    Route::resource('users', 'User\UserController')->except('create', 'edit');
    
    
});

/***********************************************************************************************************************
 *                                              Rutas Protegidas                                                       *
 ***********************************************************************************************************************/

Route::group(['middleware' => ['auth']], function () {
    
});

/***********************************************************************************************************************
 *                                              Rutas Públicas                                                         *
 ***********************************************************************************************************************/

 /***********************************************************************************************************************
 *                                              Auth Routes                                                            *
 ***********************************************************************************************************************/

Route::group(['middleware' => 'api','prefix' => 'auth' ], function ($router) {

    Route::post('register', 'User\AuthController@register')->name('api.register');
    Route::post('login', 'User\AuthController@login')->name('api.login');
    Route::post('logout', 'User\AuthController@logout')->name('api.logout');
    Route::post('refresh', 'User\AuthController@refresh')->name('api.refresh');
    Route::get('me', 'User\AuthController@me')->name('api.me');
});
