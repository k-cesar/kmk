<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StoreFlag\StoreFlag;
use App\Http\Modules\StoreChain\StoreChain;

$factory->define(StoreFlag::class, function (Faker $faker) {

    return [
        'name'     => strtoupper($faker->unique()->company),
        'store_chain_id' => StoreChain::inRandomOrder()->first() ?? factory(StoreChain::class),
    ];
});
