<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\StoreTurn\StoreTurn;

$factory->define(Deposit::class, function (Faker $faker) {

    return [
        'deposit_number' => $faker->bankAccountNumber,
        'amount'         => rand(1, 20) * 100,
        'date'           => now(),
        'store_id'       => Store::inRandomOrder()->first() ?? factory(Store::class),
        'store_turn_id'  => factory(StoreTurn::class),
        'created_by'     => User::inRandomOrder()->first() ?? factory(User::class),
    ];
});
