<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use App\Http\Modules\Presentation\Presentation;
use Faker\Generator as Faker;

$factory->define(Presentation::class, function (Faker $faker) {
    return [
        'description'           => $faker->unique()->sentence,
        'price'                 => $faker->unique()->numberBetween(20,50),
        'product_id'            => factory(Product::class),
        'is_minimal_expression' => rand(0, 1),
        'units'                 => rand(1, 10),
    ];
});
