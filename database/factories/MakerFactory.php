<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Maker\Maker;

$factory->define(Maker::class, function (Faker $faker) {

    return [
        'name' => $faker->unique()->company,
    ];
});
