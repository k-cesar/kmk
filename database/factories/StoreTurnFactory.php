<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\StoreTurn\StoreTurn;

$factory->define(StoreTurn::class, function (Faker $faker) {
    return [
        'store_id'                  => factory(Store::class),
        'turn_id'                   => factory(Turn::class),
        'open_petty_cash_amount'    => rand(1, 20) * 100,
        'open_by'                   => factory(User::class),
        'closed_by'                 => factory(User::class),
        'closed_petty_cash_amount'  => rand(1, 20) * 100,
        'open_date'                 => $faker->date(),
        'close_date'                => $faker->date(),
        'is_open'                   => rand(0, 1),
        'expenses_in_not_purchases' => rand(1, 20) * 100,
        'expenses_reason'           => $faker->sentence,
        'card_sales'                => rand(1, 20) * 100,
        'cash_on_hand'              => rand(1, 20) * 100,
    ];
});
