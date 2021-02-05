<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Currency\Currency;

$factory->define(Currency::class, function (Faker $faker) {
    return [
        'name'         => strtoupper($faker->unique()->sentence),
        'symbol'       => $faker->regexify('..'),
        'abbreviation' => strtoupper(Str::random(16)),
        'description'  => $faker->paragraph(),
        'disabled'     => rand(0, 1),
    ];
});
