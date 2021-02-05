<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;
use App\Http\Modules\ProductCategory\ProductCategory;

$factory->define(ProductSubcategory::class, function (Faker $faker) {
    return [
        'name'                => strtoupper($faker->unique()->sentence),
        'product_category_id' => ProductCategory::inRandomOrder()->first() ?? factory(ProductCategory::class),
    ];
});
