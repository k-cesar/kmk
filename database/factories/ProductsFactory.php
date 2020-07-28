<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Products\Products;
use App\Http\Modules\Uom\Uom;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use App\Http\Modules\Brand\Brand;

$factory->define(Products::class, function (Faker $faker) {
    return [
        'description' => $faker->unique()->company,
        'brand_id' => factory(Brand::class),
        'product_category_id' => factory(ProductCategory::class),
        'product_subcategory_id' => factory(ProductSubCategories::class),
        'is_taxable' => 1,
        'is_inventoriable' => 1,
        'uom_id' => factory(Uom::class),
        'minimal_expresion' => $faker->unique()->company,
        'suggested_price' => 10,
    ];
});
