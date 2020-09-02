<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Http\Modules\Zone\Zone;
use App\Http\Modules\State\State;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Company\Company;
use App\Http\Modules\StoreFlag\StoreFlag;
use App\Http\Modules\StoreType\StoreType;
use App\Http\Modules\StoreChain\StoreChain;
use App\Http\Modules\StoreFormat\StoreFormat;
use App\Http\Modules\LocationType\LocationType;
use App\Http\Modules\Municipality\Municipality;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

$factory->define(Store::class, function (Faker $faker) {

    return [
        'name'                   => $faker->unique()->company,
        'address'                => $faker->address,
        'petty_cash_amount'      => $faker->randomFloat(3, 0, 9999999),
        'store_type_id'          => factory(StoreType::class)->create(),
        'store_chain_id'         => factory(StoreChain::class)->create(),
        'store_flag_id'          => factory(StoreFlag::class)->create(),
        'location_type_id'       => factory(LocationType::class)->create(),
        'store_format_id'        => factory(StoreFormat::class)->create(),
        'company_id'             => factory(Company::class)->create(),
        'size'                   => rand(1, 100),
        'socioeconomic_level_id' => factory(SocioeconomicLevel::class)->create(),
        'state_id'               => factory(State::class)->create(),
        'municipality_id'        => factory(Municipality::class)->create(),
        'zone_id'                => factory(Zone::class)->create(),
        'latitute'               => $faker->randomFloat(7, -90, 90),
        'longitude'              => $faker->randomFloat(7, -180, 180),
    ];
});
