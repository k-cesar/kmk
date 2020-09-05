<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Store\Store;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\CashAdjustment\CashAdjustment;

$factory->define(CashAdjustment::class, function (Faker $faker) {
    return [
        'store_id'          => factory(Store::class),
        'store_turn_id'     => factory(StoreTurn::class),
        'amount'            => rand(0, 1000),
        'modification_type' => $faker->randomElement(CashAdjustment::getOptionsModificationTypes()),
        'description'       => $faker->company,
    ];
});
