<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Sell\SellInvoice;

$factory->define(SellInvoice::class, function (Faker $faker) {

    return [
        'company_id'         => factory(Company::class),
        'invoice'            => Str::uuid(),
        'sell_id'            => factory(Sell::class),
        'nit'                => $faker->bankAccountNumber,
        'name'               => $faker->name,
        'date'               => now(),
        'total'              => rand(1, 20) * 100,
        'concilation_status' => SellInvoice::OPTION_CONCILATION_STATUS_PENDING,
    ];
});
