<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Currency\Currency;

$factory->define(Currency::class, function (Faker $faker) {
    return [
        'symbol'        => $faker->currencyCode,
        'description'   => $faker->words(2, true),
        'active'        => Arr::random(Currency::getActiveOptions()),
        'main_currency' => Arr::random(Currency::getMainCurrencyOptions()),
    ];
});
