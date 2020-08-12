<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StockCounts\StockCounts;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;

$factory->define(StockCounts::class, function (Faker $faker) {
    return [
        'count_date' => $faker->date(),
        'store_id' => factory(Store::class),
        'status' => $faker->randomElement(StockCounts::getOptionsStatus()),
        'created_by' => factory(User::class),
    ];
});