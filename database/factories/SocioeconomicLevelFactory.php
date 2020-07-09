<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

$factory->define(SocioeconomicLevel::class, function (Faker $faker) {

    return [
        'name' => $faker->unique()->name(),
        'is_all_countries' => rand(0, 1),
    ];
});
