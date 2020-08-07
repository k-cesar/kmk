<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Spatie\Permission\Models\Permission;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'name'  => $faker->unique()->words(2, true),
        'group' => $faker->domainWord,
        'level' => PHP_INT_MAX,
    ];
});
