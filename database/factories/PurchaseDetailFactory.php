<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use App\Http\Modules\Purchase\Purchase;
use Faker\Generator as Faker;
use App\Http\Modules\Purchase\PurchaseDetail;

$factory->define(PurchaseDetail::class, function (Faker $faker) {

    return [
        'purchase_id' => factory(Purchase::class),
        'product_id'  => factory(Product::class),
        'item_line'   => $faker->randomNumber(),
        'quantity'    => rand(1, 100),
        'unit_price'  => rand(1, 100) * 100,
    ];
});
