<?php

namespace App\Providers;

use App\Rules\StoreVisible;
use App\Rules\PaymentMethodVisible;
use Illuminate\Support\ServiceProvider;
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
        Validator::extend('store_visible', StoreVisible::class, (new StoreVisible)->message());
        Validator::extend('payment_method_visible', PaymentMethodVisible::class, (new PaymentMethodVisible)->message());
    }
}
