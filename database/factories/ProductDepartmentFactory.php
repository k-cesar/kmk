<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\ProductDepartment\ProductDepartment;

$factory->define(ProductDepartment::class, function (Faker $faker) {
    return [
        'name'         => $faker->unique()->sentence()       
    ];
});
