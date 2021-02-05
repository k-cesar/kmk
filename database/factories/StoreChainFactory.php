<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StoreChain\StoreChain;

$factory->define(StoreChain::class, function (Faker $faker) {
    return [
        'name' => strtoupper($faker->unique()->sentence),
    ];
});
