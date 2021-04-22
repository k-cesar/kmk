<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Brand\Brand;
use App\Http\Modules\Maker\Maker;
use App\Http\Modules\Company\Company;

$factory->define(Brand::class, function (Faker $faker) {

    return [
        'name'       => $faker->unique()->company,
        'maker_id'   => Maker::inRandomOrder()->first() ?? factory(Maker::class),
        'company_id' => Company::inRandomOrder()->first() ?? factory(Company::class),
    ];
});
