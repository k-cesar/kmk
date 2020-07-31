<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\PresentationCombo\PresentationCombo;
use App\Http\Modules\Uom\Uom;

$factory->define(PresentationCombo::class, function (Faker $faker) {

    return [
        'description'       => $faker->unique()->sentence,
        'uom_id'            => factory(Uom::class),
        'minimal_expresion' => $faker->sentence,
        'suggested_price'   => rand(1, 50),
    ];
});
