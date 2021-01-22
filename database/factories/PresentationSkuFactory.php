<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\PresentationSku\PresentationSku;

$factory->define(PresentationSku::class, function (Faker $faker) {

    return [
        'company_id'       => Company::inRandomOrder()->first() ?? factory(Company::class),
        'code'             => $faker->unique()->bankAccountNumber,
        'description'      => $faker->sentence,
        'presentation_id'  => factory(Presentation::class),
        'seasonal_product' => rand(0, 1),
    ];
});
