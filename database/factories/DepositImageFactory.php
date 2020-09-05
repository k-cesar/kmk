<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Deposit\DepositImage;

$factory->define(DepositImage::class, function (Faker $faker) {

    return [
        'url'        => $faker->url,
        'deposit_id' => factory(Deposit::class),
    ];
});
