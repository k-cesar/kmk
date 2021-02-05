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
        'name'                   => strtoupper($faker->unique()->company),
        'address'                => $faker->address,
        'petty_cash_amount'      => $faker->randomFloat(3, 0, 9999999),
        'store_type_id'          => StoreType::inRandomOrder()->first() ?? factory(StoreType::class),
        'store_chain_id'         => StoreChain::inRandomOrder()->first() ?? factory(StoreChain::class),
        'store_flag_id'          => StoreFlag::inRandomOrder()->first() ?? factory(StoreFlag::class),
        'location_type_id'       => LocationType::inRandomOrder()->first() ?? factory(LocationType::class),
        'store_format_id'        => StoreFormat::inRandomOrder()->first() ?? factory(StoreFormat::class),
        'company_id'             => Company::inRandomOrder()->first() ?? factory(Company::class),
        'size'                   => rand(1, 100),
        'socioeconomic_level_id' => SocioEconomicLevel::inRandomOrder()->first() ?? factory(SocioEconomicLevel::class),
        'state_id'               => State::inRandomOrder()->first() ?? factory(State::class),
        'municipality_id'        => Municipality::inRandomOrder()->first() ?? factory(Municipality::class),
        'zone_id'                => Zone::inRandomOrder()->first() ?? factory(Zone::class),
        'latitute'               => $faker->randomFloat(7, -90, 90),
        'longitude'              => $faker->randomFloat(7, -180, 180),
    ];
});
