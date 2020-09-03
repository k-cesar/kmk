<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\StoreTurn\StoreTurn;

$factory->define(StoreTurn::class, function (Faker $faker) {
    return [
        'store_id'                 => factory(Store::class),
        'turn_id'                  => factory(Turn::class),
        'open_petty_cash_amount'   => rand(1, 20) * 100,
        'open_by'                  => factory(User::class),
        'closed_by'                => factory(User::class),
        'closed_petty_cash_amount' => rand(1, 20) * 100,
        'open_date'                => now(),
        'close_date'               => now(),
        'is_open'                  => rand(0, 1),
    ];
});
