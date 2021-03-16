<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\StoreFormat\StoreFormat;

$factory->define(StoreFormat::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence,
    ];
});
