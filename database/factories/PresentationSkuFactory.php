<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\PresentationSku\PresentationSku;
use App\Http\Modules\ProductPresentation\ProductPresentation;

$factory->define(PresentationSku::class, function (Faker $faker) {

    return [
        'code'                    => $faker->unique()->bankAccountNumber,
        'description'             => $faker->sentence,
        'product_presentation_id' => factory(ProductPresentation::class)->create(),
        'seasonal_product'        => rand(0, 1),
    ];
});
