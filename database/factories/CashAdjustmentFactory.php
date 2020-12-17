<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Store\Store;
use App\Http\Modules\CashAdjustment\CashAdjustment;

$factory->define(CashAdjustment::class, function (Faker $faker) {
    return [
        'store_id'      => Store::inRandomOrder()->first() ?? factory(Store::class),
        'amount'        => rand(1, 20) * 100,
        'type'          => $faker->randomElement(CashAdjustment::getOptionsTypes()),
        'description'   => $faker->company,
    ];
});
