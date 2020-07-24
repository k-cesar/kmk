<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use App\Http\Modules\ProductCategory\ProductCategory;

$factory->define(ProductSubCategories::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(),
        'product_category_id' => factory(ProductCategory::class)
    ];
});
