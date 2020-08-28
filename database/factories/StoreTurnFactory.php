<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;
use App\Http\Modules\StoreTurn\StoreTurn;

$factory->define(StoreTurn::class, function (Faker $faker) {
    return [
        'store_id'                  => factory(Store::class)->create(),
        'turn_id'                   => factory(Turn::class)->create(),
        'open_petty_cash_amount'    => $faker->randomFloat(3, 0, 9999999),
        'open_by'                   => factory(User::class)->create(),
        'closed_by'                 => factory(User::class)->create(),
        'closed_petty_cash_amount'  => $faker->randomFloat(3, 0, 9999999),
        'open_date'                 => $faker->date(),
        'close_date'                => $faker->date(),
        'is_open'                   => rand(0, 1),
    ];
});
