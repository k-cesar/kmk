<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\Client\Client;
use App\Http\Modules\Country\Country;

$factory->define(Client::class, function (Faker $faker) {

    return [
        'name'         => $faker->name ,
        'type'         => $faker->randomElement(Client::getOptionsTypes()) ,
        'country_id'   => Country::inRandomOrder()->first() ?? factory(Country::class) ,
        'nit'          => strtoupper($faker->bothify('##??##??')),
        'address'      => $faker->address ,
        'sex'          => $faker->randomElement(Client::getOptionsSex()) ,
        'biometric_id' => Str::random(50) ,
        'birthdate'    => $faker->date() ,
    ];
});
