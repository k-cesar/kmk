<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Provider\Provider;

$factory->define(Provider::class, function (Faker $faker) {

    return [
        'name'       => $faker->unique()->company,
        'nit'        => strtoupper($faker->bothify('##??##??')),
        'country_id' => Country::inRandomOrder()->first() ?? factory(Country::class),
        'company_id' => Company::inRandomOrder()->first() ?? factory(Company::class),
    ];
});
