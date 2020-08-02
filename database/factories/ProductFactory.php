<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Uom\Uom;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;
use App\Http\Modules\Brand\Brand;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'description'            => $faker->unique()->company,
        'brand_id'               => factory(Brand::class),
        'product_category_id'    => factory(ProductCategory::class),
        'product_subcategory_id' => factory(ProductSubcategory::class),
        'is_taxable'             => rand(0, 1),
        'is_inventoriable'       => rand(0, 1),
        'uom_id'                 => factory(Uom::class),
        'minimal_expresion'      => $faker->unique()->company,
        'suggested_price'        => rand(0, 20),
        'is_all_countries'       => rand(0, 1),
    ];
});
