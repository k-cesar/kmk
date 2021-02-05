<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StoreType\StoreType;

$factory->define(StoreType::class, function (Faker $faker) {
    return [
        'name' => strtoupper($faker->unique()->sentence),
    ];
});
