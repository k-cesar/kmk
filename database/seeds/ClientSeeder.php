<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Http\Modules\Client\Client;
use App\Http\Modules\Country\Country;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Client::class)->create([
            'id'           => 0,
            'name'         => 'OFFLINE CLIENT',
            'type'         => Client::OPTION_TYPE_ADMIN,
            'country_id'   => Country::inRandomOrder()->first() ?? factory(Country::class),
            'nit'          => '0000000',
            'address'      => 'Dirección 0',
            'sex'          => Client::OPTION_SEX_MALE,
            'biometric_id' => Str::random(50),
            'birthdate'    => now(),
            'deleted_at'   => now(),
        ]);
        
        factory(Client::class, 5)->create();
    }
}
