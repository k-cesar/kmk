<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\PresentationSku\PresentationSku;
use App\Http\Modules\Presentation\Presentation;

$factory->define(PresentationSku::class, function (Faker $faker) {

    return [
        'code'             => $faker->unique()->bankAccountNumber,
        'description'      => $faker->sentence,
        'presentation_id'  => factory(Presentation::class),
        'seasonal_product' => rand(0, 1),
    ];
});
