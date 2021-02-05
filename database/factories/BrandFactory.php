<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Brand\Brand;
use App\Http\Modules\Maker\Maker;

$factory->define(Brand::class, function (Faker $faker) {

    return [
        'name'     => strtoupper($faker->unique()->company),
        'maker_id' => Maker::inRandomOrder()->first() ?? factory(Maker::class),
    ];
});
