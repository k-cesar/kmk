<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Arr;
use Faker\Generator as Faker;
use App\Http\Modules\Company\Company;

$factory->define(Company::class, function (Faker $faker) {

    return [
        'nit'               => $faker->bankAccountNumber,
        'name'              => $faker->company,
        'comercial_name'    => $faker->company,
        'comercial_address' => $faker->address,
        'active'            => Arr::random(Company::getActiveOptions()),
        'currency_id'       => 1,
    ];
});
