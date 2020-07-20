<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\ProductCategory\ProductCategory;
use App\Http\Modules\ProductDepartment\ProductDepartment;

$factory->define(ProductCategory::class, function (Faker $faker) {
    return [
        'name'         => $faker->unique()->sentence(),
        'product_department_id' =>factory(ProductDepartment::class)
    ];
});
