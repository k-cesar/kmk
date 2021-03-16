<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'name'        => $faker->unique()->country,
        'currency_id' => Currency::inRandomOrder()->first() ?? factory(Currency::class),
    ];
});
