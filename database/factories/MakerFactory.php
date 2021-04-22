<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Maker\Maker;
use App\Http\Modules\Company\Company;

$factory->define(Maker::class, function (Faker $faker) {

    return [
        'name'       => $faker->unique()->company,
        'company_id' => Company::inRandomOrder()->first() ?? factory(Company::class),
    ];
});
