<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;

$factory->define(Company::class, function (Faker $faker) {

    return [
        'name'               => $faker->company,
        'reason'             => $faker->paragraph,
        'regime'             => $faker->sentence(2),
        'nit'                => strtoupper($faker->bothify('##??##??')),
        'phone'              => rand(100000,9999999),
        'address'            => $faker->address,
        'country_id'         => Country::inRandomOrder()->first() ?? factory(Country::class),
        'currency_id'        => Currency::inRandomOrder()->first() ?? factory(Currency::class),
        'allow_fel'          => rand(0, 1),
        'allow_add_users'    => rand(0, 1),
        'allow_add_stores'   => rand(0, 1),
        'allow_add_products' => rand(0, 1),
    ];
});
