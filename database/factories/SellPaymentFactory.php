<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\Sell\SellPayment;
use App\Http\Modules\PaymentMethod\PaymentMethod;

$factory->define(SellPayment::class, function (Faker $faker) {

    return [
        'sell_id'           => factory(Sell::class),
        'amount'            => rand(1, 2) * 100,
        'card_four_digits'  => $faker->randomNumber(4),
        'authorization'     => Str::uuid(),
        'status'            => $faker->randomElement([SellPayment::OPTION_STATUS_VERIFIED, SellPayment::OPTION_STATUS_UNVERIFIED]),
        'payment_method_id' => factory(PaymentMethod::class),
    ];
});
