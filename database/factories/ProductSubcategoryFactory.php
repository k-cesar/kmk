<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;
use App\Http\Modules\ProductCategory\ProductCategory;

$factory->define(ProductSubcategory::class, function (Faker $faker) {
    return [
        'name'                => $faker->unique()->sentence(),
        'product_category_id' => factory(ProductCategory::class),
    ];
});
