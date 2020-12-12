<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Sell\Sell;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Client\Client;
use App\Http\Modules\StoreTurn\StoreTurn;

$factory->define(Sell::class, function (Faker $faker) {

    return [
        'store_id'      => Store::inRandomOrder()->first() ?? factory(Store::class),
        'client_id'     => factory(Client::class),
        'description'   => $faker->sentence,
        'date'          => now(),
        'total'         => rand(1, 30) * 100,
        'seller_id'     => User::inRandomOrder()->first() ?? factory(User::class),
        'status'        => $faker->randomElement(Sell::getOptionsStatus()),
        'status_dte'    => $faker->randomElement(Sell::getOptionsStatusDTE()),
        'invoice_link'  => $faker->url,
        'store_turn_id' => factory(StoreTurn::class),
    ];
});
