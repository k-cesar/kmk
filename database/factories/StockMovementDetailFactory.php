<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Product\Product;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\Stock\StockMovementDetail;
use App\Http\Modules\Stock\StockStore;
use Faker\Generator as Faker;

$factory->define(StockMovementDetail::class, function (Faker $faker, Array $attributes = []) {

    return [
        'stock_movement_id'     => factory(StockMovement::class),
        'stock_store_id'        => factory(StockStore::class),
        'product_id'            => factory(Product::class),
        'quantity'              => rand(1, 100),
        'product_unit_price'    => rand(1, 100),
        'avg_product_unit_cost' => rand(1, 100),
    ];
});
