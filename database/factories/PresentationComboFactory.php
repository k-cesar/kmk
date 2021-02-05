<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Company\Company;
use App\Http\Modules\PresentationCombo\PresentationCombo;

$factory->define(PresentationCombo::class, function (Faker $faker) {

    return [
        'company_id'      => Company::inRandomOrder()->first() ?? factory(Company::class),
        'description'     => strtoupper($faker->unique()->sentence),
        'suggested_price' => rand(1, 50) * 100,
    ];
});
