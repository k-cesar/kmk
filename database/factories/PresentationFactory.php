<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use App\Http\Modules\Presentation\Presentation;
use Faker\Generator as Faker;

$factory->define(Presentation::class, function (Faker $faker) {

    $isGrouping = rand(0, 1);

    $units = $isGrouping ? rand(2, 10) : 1;

    return [
        'description'           => $faker->unique()->sentence,
        'price'                 => rand(1, 20) * 100,
        'product_id'            => factory(Product::class),
        'is_grouping'           => $isGrouping,
        'units'                 => $units,
    ];
});
