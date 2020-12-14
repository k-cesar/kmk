<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Product\Product;
use App\Http\Modules\StockCount\StockCount;
use App\Http\Modules\StockCount\StockCountDetail;

$factory->define(StockCountDetail::class, function (Faker $faker) {
    return [
        'stock_count_id' => factory(StockCount::class),
        'product_id' => factory(Product::class),
        'quantity' => 50,
    ];
});
