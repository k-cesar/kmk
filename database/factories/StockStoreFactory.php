<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use Faker\Generator as Faker;
use App\Http\Modules\Stock\StockStore;
use App\Http\Modules\Store\Store;

$factory->define(StockStore::class, function (Faker $faker) {

    return [
        'store_id'   => Store::inRandomOrder()->first() ?? factory(Store::class),
        'product_id' => factory(Product::class),
        'quantity'   => rand(1, 100),
    ];
});
