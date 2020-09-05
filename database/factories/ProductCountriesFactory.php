<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\ProductCountries\ProductCountries;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Country\Country;


$factory->define(ProductCountries::class, function (Faker $faker) {
    return [
        'product_id' => factory(Product::class),
        'country_id' => Country::inRandomOrder()->first() ?? factory(Country::class),
    ];
});
