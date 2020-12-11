<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Store\Store;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

$factory->define(StoreTurnModification::class, function (Faker $faker) {
    return [
        'store_id'          => Store::inRandomOrder()->first() ?? factory(Store::class),
        'store_turn_id'     => factory(StoreTurn::class),
        'amount'            => rand(0, 1000),
        'modification_type' => $faker->randomElement(StoreTurnModification::getOptionsModificationTypes()),
        'description'       => $faker->company,
    ];
});
