<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\PaymentMethod\PaymentMethod;
use App\Http\Modules\Provider\Provider;
use Faker\Generator as Faker;
use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\Store\Store;
use App\Http\Modules\User\User;

$factory->define(Purchase::class, function (Faker $faker) {

    return [
        'store_id'          => Store::inRandomOrder()->first() ?? factory(Store::class),
        'user_id'           => User::inRandomOrder()->first() ?? factory(User::class),
        'comments'          => $faker->sentence,
        'invoice'           => $faker->bankAccountNumber,
        'date'              => now(),
        'total'             => rand(1, 100) * 200,
        'provider_id'       => factory(Provider::class),
        'payment_method_id' => factory(PaymentMethod::class),
    ];
});
