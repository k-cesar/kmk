<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\Purchase\PurchaseDetail;
use App\Http\Modules\Presentation\Presentation;

$factory->define(PurchaseDetail::class, function (Faker $faker) {

    $quantity  = rand(1, 100);
    $unitPrice = rand(1, 100) * 100;
    $total     = $quantity * $unitPrice;

    return [
        'purchase_id'     => factory(Purchase::class),
        'presentation_id' => factory(Presentation::class),
        'item_line'       => $faker->randomNumber(),
        'quantity'        => $quantity,
        'unit_price'      => $unitPrice,
        'total'           => $total,
    ];
});
