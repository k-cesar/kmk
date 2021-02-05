<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Region\Region;
use App\Http\Modules\Country\Country;

$factory->define(Region::class, function (Faker $faker) {

    return [
        'name'       => strtoupper($faker->unique()->company),
        'country_id' => Country::inRandomOrder()->first() ?? factory(Country::class),
    ];
});
