<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\State\State;
use App\Http\Modules\Region\Region;

$factory->define(State::class, function (Faker $faker) {

    return [
        'name'      => $faker->unique()->company,
        'region_id' => Region::inRandomOrder()->first() ?? factory(Region::class),
    ];
});
