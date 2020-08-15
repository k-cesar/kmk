<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;
use App\Http\Modules\StockCounts\StockCounts;
use App\Http\Modules\Product\Product;

$factory->define(StockCountsDetail::class, function (Faker $faker) {
    return [
        'stock_count_id' => factory(StockCounts::class),
        'product_id' => factory(Product::class),
        'quantity' => 50,
    ];
});
