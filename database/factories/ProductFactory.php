<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Uom\Uom;
use Faker\Generator as Faker;
use App\Http\Modules\Brand\Brand;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Product\Product;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'company_id'             => Company::inRandomOrder()->first() ?? factory(Company::class),
        'description'            => $faker->unique()->company,
        'brand_id'               => Brand::inRandomOrder()->first() ?? factory(Brand::class),
        'product_category_id'    => ProductCategory::inRandomOrder()->first() ?? factory(ProductCategory::class),
        'product_subcategory_id' => ProductSubCategory::inRandomOrder()->first() ?? factory(ProductSubCategory::class),
        'is_taxable'             => rand(0, 1),
        'is_inventoriable'       => rand(0, 1),
        'uom_id'                 => Uom::inRandomOrder()->first() ?? factory(Uom::class),
        'suggested_price'        => rand(1, 10) * 100,
        'is_all_countries'       => rand(0, 1),
    ];
});
