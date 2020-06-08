<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Spatie\Permission\Models\Role;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name'  => $faker->unique()->domainWord,
        'level' => PHP_INT_MAX,
    ];
});
