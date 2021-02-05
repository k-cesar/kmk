<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\PaymentMethod\PaymentMethod;
use App\Http\Modules\Company\Company;

$factory->define(PaymentMethod::class, function (Faker $faker) {

    return [
        'name'       => strtoupper($faker->unique()->company),
        'company_id' => Company::inRandomOrder()->first() ?? factory(Company::class),
    ];
});
