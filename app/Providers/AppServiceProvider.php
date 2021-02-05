<?php

namespace App\Providers;

use App\Rules\IUniqueRule;
use App\Rules\StoreVisibleRule;
use Illuminate\Support\ServiceProvider;
use App\Rules\VisibleThroughCompanyRule;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('store_visible', StoreVisibleRule::class, (new StoreVisibleRule)->message());
        Validator::extend('visible_through_company', VisibleThroughCompanyRule::class, (new VisibleThroughCompanyRule)->message());
        Validator::extend('iunique', IUniqueRule::class, (new IUniqueRule)->message());
    }
}
