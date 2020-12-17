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
    Route::resource('permissions', 'Permission\PermissionController')->only('index');
    
    Route::resource('currencies', 'Currency\CurrencyController')->except('create', 'edit');
    
    Route::resource('countries', 'Country\CountryController')->except('create', 'edit');
    
    Route::resource('companies', 'Company\CompanyController')->except('create', 'edit');

    Route::resource('users', 'User\UserController')->except('create', 'edit');

    Route::resource('users.permissions', 'User\UserPermissionController')->only('index', 'store');

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

    Route::resource('product-countries', 'ProductCountries\ProductCountriesController')->except('create', 'edit');

    Route::resource('product-subcategories', 'ProductSubcategory\ProductSubcategoryController')->except('create', 'edit');

    Route::resource('presentations', 'Presentation\PresentationController')->except('create', 'edit');

    Route::resource('products', 'Product\ProductController')->except('create', 'edit');
    
    Route::resource('store-formats', 'StoreFormat\StoreFormatController')->except('create', 'edit');
    
    Route::resource('store-chains', 'StoreChain\StoreChainController')->except('create', 'edit');
    
    Route::resource('store-flags', 'StoreFlag\StoreFlagController')->except('create', 'edit');
    
    Route::resource('regions', 'Region\RegionController')->except('create', 'edit');
    
    Route::resource('states', 'State\StateController')->except('create', 'edit');
    
    Route::resource('municipalities', 'Municipality\MunicipalityController')->except('create', 'edit');
    
    Route::resource('zones', 'Zone\ZoneController')->except('create', 'edit');
    
    Route::resource('stores', 'Store\StoreController')->except('create', 'edit');

    Route::resource('clients', 'Client\ClientController')->except('create', 'edit');
    
    Route::resource('turns', 'Turn\TurnController')->except('create', 'edit');

    Route::resource('presentation-skus', 'PresentationSku\PresentationSkuController')->except('create', 'edit');
    
    Route::resource('presentation-combos', 'PresentationCombo\PresentationComboController')->except('create', 'edit');
    
    Route::resource('stocks', 'Stock\StockController')->only('index');
    
    Route::resource('purchases', 'Purchase\PurchaseController')->except('create', 'edit', 'destroy');
    
    Route::resource('transfers', 'Transfer\TransferController')->only('index', 'store');
    
    Route::resource('adjustments', 'Adjustment\AdjustmentController')->only('index', 'store');

    Route::resource('stock-counts-adjustments', 'StockCount\StockCountAdjustmentController')->only('store');

    Route::resource('stock-counts', 'StockCount\StockCountController')->except('create', 'edit', 'destroy');

    Route::resource('sells', 'Sell\SellController')->except('create', 'edit', 'update');
    Route::post('sells-offline', 'Sell\SellController@storeOffline')->name('sells-offline.store');
    
    Route::resource('sell-payments', 'SellPayment\SellPaymentController')->only('index', 'update');
    
    Route::resource('deposits', 'Deposit\DepositController')->except('create', 'edit', 'destroy');
    
    Route::resource('store-turns', 'StoreTurn\StoreTurnController')->except('create', 'edit');
    
    Route::resource('stores.turns.items', 'Sell\StoreTurnItemController')->only('index');
    
    Route::resource('cash-adjustments', 'CashAdjustment\CashAdjustmentController')->only('store');
    
    Route::resource('stores-cash', 'CashAdjustment\StoreCashController')->only('index');
});

/***********************************************************************************************************************
 *                                              Rutas Protegidas                                                       *
 ***********************************************************************************************************************/

Route::group(['middleware' => ['auth']], function () {
    
    Route::get('clients-options', 'Client\ClientController@options')->name('clients.options');
    
    Route::get('brands-options', 'Brand\BrandController@options')->name('brands.options');
    
    Route::get('companies-options', 'Company\CompanyController@options')->name('companies.options');
    
    Route::get('countries-options', 'Country\CountryController@options')->name('countries.options');
    
    Route::get('currencies-options', 'Currency\CurrencyController@options')->name('currencies.options');
    
    Route::get('location-types-options', 'LocationType\LocationTypeController@options')->name('location-types.options');
    
    Route::get('makers-options', 'Maker\MakerController@options')->name('makers.options');
    
    Route::get('municipalities-options', 'Municipality\MunicipalityController@options')->name('municipalities.options');
    
    Route::get('payment-methods-options', 'PaymentMethod\PaymentMethodController@options')->name('payment-methods.options');
    
    Route::get('product-categories-options', 'ProductCategory\ProductCategoryController@options')->name('product-categories.options');
    
    Route::get('product-departments-options', 'ProductDepartment\ProductDepartmentController@options')->name('product-departments.options');
    
    Route::get('product-subcategories-options', 'ProductSubcategory\ProductSubcategoryController@options')->name('product-subcategories.options');
    
    Route::get('regions-options', 'Region\RegionController@options')->name('regions.options');
    
    Route::get('roles-options', 'Role\RoleController@options')->name('roles.options');
    
    Route::get('socioeconomic-levels-options', 'SocioeconomicLevel\SocioeconomicLevelController@options')->name('socioeconomic-levels.options');
    
    Route::get('states-options', 'State\StateController@options')->name('states.options');
    
    Route::get('stores-options', 'Store\StoreController@options')->name('stores.options');
    
    Route::get('store-chains-options', 'StoreChain\StoreChainController@options')->name('store-chains.options');
    
    Route::get('store-flags-options', 'StoreFlag\StoreFlagController@options')->name('store-flags.options');
    
    Route::get('store-formats-options', 'StoreFormat\StoreFormatController@options')->name('store-formats.options');
    
    Route::get('store-types-options', 'StoreType\StoreTypeController@options')->name('store-types.options');

    Route::get('turns-options', 'Turn\TurnController@options')->name('turns.options');
    
    Route::get('uoms-options', 'Uom\UomController@options')->name('uoms.options');
    
    Route::get('zones-options', 'Zone\ZoneController@options')->name('zones.options');
    
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
    Route::put('reset', 'User\AuthController@reset')->name('api.reset');
    Route::post('logout', 'User\AuthController@logout')->name('api.logout');
    Route::get('refresh', 'User\AuthController@refresh')->name('api.refresh');
    Route::get('me', 'User\AuthController@me')->name('api.me');
});
