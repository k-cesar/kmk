<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use App\Http\Modules\Purchase\Purchase;
use Faker\Generator as Faker;
use App\Http\Modules\Purchase\PurchaseDetail;

$factory->define(PurchaseDetail::class, function (Faker $faker) {

    $quantity  = rand(1, 100);
    $unitPrice = rand(1, 100) * 100;
    $total     = $quantity * $unitPrice;

    return [
        'purchase_id' => factory(Purchase::class),
        'product_id'  => factory(Product::class),
        'item_line'   => $faker->randomNumber(),
        'quantity'    => $quantity,
        'unit_price'  => $unitPrice,
        'total'       => $total,
    ];
});
