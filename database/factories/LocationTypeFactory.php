<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\LocationType\LocationType;

$factory->define(LocationType::class, function (Faker $faker) {
    return [
        'name' => strtoupper(strtoupper($faker->unique()->sentence)),
    ];
});
