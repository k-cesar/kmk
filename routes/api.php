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
    
    Route::resource('currencies', 'Currency\CurrencyController')->except('create', 'edit');
    
    Route::resource('countries', 'Country\CountryController')->except('create', 'edit');
    
    Route::resource('companies', 'Company\CompanyController')->except('create', 'edit');

    Route::resource('users', 'User\UserController')->except('create', 'edit');

    Route::resource('makers', 'Maker\MakerController')->except('create', 'edit');

    Route::resource('brands', 'Brand\BrandController')->except('create', 'edit');
    
    Route::resource('providers', 'Provider\ProviderController')->except('create', 'edit');

    Route::resource('payment-methods', 'PaymentMethod\PaymentMethodController')->except('create', 'edit');

    Route::resource('uoms', 'Uom\UomController')->except('create', 'edit');

    Route::resource('socioeconomic-levels', 'SocioeconomicLevel\SocioeconomicLevelController')->except('create', 'edit');
    
    Route::resource('location-types', 'LocationType\LocationTypeController')->except('create', 'edit');
    
    Route::resource('store-types', 'StoreType\StoreTypeController')->except('create', 'edit');
    
    Route::resource('product-departments', 'ProductDepartment\ProductDepartmentController')->except('create', 'edit');
    
    Route::resource('product-categories', 'ProductCategory\ProductCategoryController')->except('create', 'edit');
    
    Route::resource('store-formats', 'StoreFormat\StoreFormatController')->except('create', 'edit');
    
    Route::resource('store-chains', 'StoreChain\StoreChainController')->except('create', 'edit');
    
    Route::resource('store-flags', 'StoreFlag\StoreFlagController')->except('create', 'edit');
    
    Route::resource('regions', 'Region\RegionController')->except('create', 'edit');
    
    Route::resource('states', 'State\StateController')->except('create', 'edit');
    
    Route::resource('municipalities', 'Municipality\MunicipalityController')->except('create', 'edit');
    
    Route::resource('zones', 'Zone\ZoneController')->except('create', 'edit');

});

/***********************************************************************************************************************
 *                                              Rutas Protegidas                                                       *
 ***********************************************************************************************************************/

Route::group(['middleware' => ['auth']], function () {
    
});

/***********************************************************************************************************************
 *                                              Rutas Públicas                                                         *
 ***********************************************************************************************************************/

Route::get('version', function () {
    return response()->json(['version' => '0.1']);
});

 /***********************************************************************************************************************
 *                                              Auth Routes                                                            *
 ***********************************************************************************************************************/

Route::group(['middleware' => 'api','prefix' => 'auth' ], function ($router) {

    Route::post('register', 'User\AuthController@register')->name('api.register');
    Route::post('login', 'User\AuthController@login')->name('api.login');
    Route::post('logout', 'User\AuthController@logout')->name('api.logout');
    Route::get('refresh', 'User\AuthController@refresh')->name('api.refresh');
    Route::get('me', 'User\AuthController@me')->name('api.me');
});
