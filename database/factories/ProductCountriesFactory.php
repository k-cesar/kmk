<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\ProductCountries\ProductCountries;
use App\Http\Modules\Products\Products;
use App\Http\Modules\Country\Country;


$factory->define(ProductCountries::class, function (Faker $faker) {
    return [
        'product_id' => factory(Products::class),
        'country_id' => factory(Country::class),
    ];
});
