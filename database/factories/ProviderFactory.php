<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Provider\Provider;
use App\Http\Modules\Country\Country;

$factory->define(Provider::class, function (Faker $faker) {

    return [
        'name'       => $faker->unique()->company,
        'nit'        => $faker->numerify('##############'),
        'country_id' => Country::inRandomOrder()->first() ?? factory(Country::class),
    ];
});
