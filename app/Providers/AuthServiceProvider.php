<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Http\Modules\Company\Company'             => 'App\Policies\CompanyPolicy',
        'App\Http\Modules\Deposit\Deposit'             => 'App\Policies\DepositPolicy',
        'App\Http\Modules\PaymentMethod\PaymentMethod' => 'App\Policies\PaymentMethodPolicy',
        'App\Http\Modules\Purchase\Purchase'           => 'App\Policies\PurchasePolicy',
        'App\Http\Modules\Sell\Sell'                   => 'App\Policies\SellPolicy',
        'App\Http\Modules\SellPayment\SellPayment'     => 'App\Policies\SellPaymentPolicy',
        'App\Http\Modules\StockCount\StockCount'       => 'App\Policies\StockCountPolicy',
        'App\Http\Modules\Store\Store'                 => 'App\Policies\StorePolicy',
        'App\Http\Modules\StoreTurn\StoreTurn'         => 'App\Policies\StoreTurnPolicy',
        'App\Http\Modules\Turn\Turn'                   => 'App\Policies\TurnPolicy',
        'App\Http\Modules\User\User'                   => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole(config('app.role_super_admin_name')) ? true : null;
        });
    }
}
