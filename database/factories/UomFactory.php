<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Uom\Uom;

$factory->define(Uom::class, function (Faker $faker) {

    return [
        'name'         => $faker->unique()->colorName,
        'abbreviation' => $faker->unique()->citySuffix,
        'description'  => $faker->sentence,
    ];
});
