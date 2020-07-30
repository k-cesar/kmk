<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Presentations\Presentations;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Presentations::class, function (Faker $faker) {
    return [
        'description' => $faker->unique()->company,
        'price' => $faker->unique()->numberBetween(20,50),
    ];
});
