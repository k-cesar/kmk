<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\PresentationSku\PresentationSku;

$factory->define(PresentationSku::class, function (Faker $faker) {

    return [
        'code'                    => $faker->unique()->bankAccountNumber,
        'description'             => $faker->sentence,
        'product_presentation_id' => 1,
        'seasonal_product'        => rand(0, 1),
    ];
});
