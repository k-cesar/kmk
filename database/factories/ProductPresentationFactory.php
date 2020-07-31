<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\ProductPresentation\ProductPresentation;
use Faker\Generator as Faker;

$factory->define(ProductPresentation::class, function (Faker $faker) {
    return [
        'description' => $faker->unique()->company,
        'price' => $faker->unique()->numberBetween(20,50),
    ];
});
