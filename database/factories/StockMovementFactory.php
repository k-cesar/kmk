<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\Store\Store;
use App\Http\Modules\User\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(StockMovement::class, function (Faker $faker, Array $attributes = []) {

    
    $originType = $attributes['origin_type'] ?? $faker->randomElement(Arr::except(StockMovement::getOptionsOriginTypes(), 4));

    $originIds = [
        StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT => rand(100, 5000),
        StockMovement::OPTION_ORIGIN_TYPE_COUNT             => rand(100, 5000),
        StockMovement::OPTION_ORIGIN_TYPE_PURCHASE          => factory(Purchase::class),
        StockMovement::OPTION_ORIGIN_TYPE_SELL              => rand(100, 5000),
    ];

    $movementTypes = [
        StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT => StockMovement::OPTION_MOVEMENT_TYPE_ADJUSTMENT,
        StockMovement::OPTION_ORIGIN_TYPE_COUNT             => StockMovement::OPTION_MOVEMENT_TYPE_ADJUSTMENT,
        StockMovement::OPTION_ORIGIN_TYPE_PURCHASE          => StockMovement::OPTION_MOVEMENT_TYPE_INPUT,
        StockMovement::OPTION_ORIGIN_TYPE_SELL              => StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT,
    ];

    return [
        'description'   => $faker->sentence,
        'user_id'       => factory(User::class),
        'origin_type'   => $originType,
        'origin_id'     => $originIds[$originType],
        'date'          => now(),
        'movement_type' => $movementTypes[$originType],
        'store_id'      => factory(Store::class),
    ];
});
