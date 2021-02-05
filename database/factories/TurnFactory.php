<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use Carbon\Carbon;

$factory->define(Turn::class, function (Faker $faker) {

    $startTime = Carbon::createFromFormat('H:i:s', $faker->time('H:i:s'));

    return [
        'name'       => strtoupper($faker->unique()->city),
        'store_id'   => Store::inRandomOrder()->first() ?? factory(Store::class),
        'start_time' => $startTime->format('H:i:s'),
        'end_time'   => $startTime->addSecond()->format('H:i:s'),
        'is_active'  => rand(0, 1),
        'is_default' => rand(0, 1),
    ];
});
