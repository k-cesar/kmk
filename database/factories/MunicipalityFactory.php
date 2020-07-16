<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Municipality\Municipality;
use App\Http\Modules\State\State;

$factory->define(Municipality::class, function (Faker $faker) {

    return [
        'name'     => $faker->unique()->company,
        'state_id' => factory(State::class)->create(),
    ];
});
