<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StockCount\StockCount;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;

$factory->define(StockCount::class, function (Faker $faker) {
    return [
        'count_date' => $faker->date(),
        'store_id'   => Store::inRandomOrder()->first() ?? factory(Store::class),
        'status'     => $faker->randomElement(StockCount::getOptionsStatus()),
        'created_by' => User::inRandomOrder()->first() ?? factory(User::class),
    ];
});