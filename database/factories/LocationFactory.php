<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Http\Modules\Company\Company;
use App\Http\Modules\Location\Location;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'company_id'        => factory(Company::class)->create(),
        'name'              => $faker->domainName,
        'active'            => Arr::random(Location::getActiveOptions()),
        'type'              => $faker->creditCardType,
        'municipalities_id' => 1,
    ];
});
