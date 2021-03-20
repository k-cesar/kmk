<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Zone\Zone;
use App\Http\Modules\Municipality\Municipality;

$factory->define(Zone::class, function (Faker $faker) {

    return [
        'name'            => $faker->unique()->company,
        'municipality_id' => Municipality::inRandomOrder()->first() ?? factory(Municipality::class),
    ];
});
