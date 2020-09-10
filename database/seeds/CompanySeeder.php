<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Company::class)->create([
            'id'                    => '0',
            'name'                  => 'Empresa 0',
            'reason'                => 'Razón Social 0',
            'nit'                   => '00000000',
            'phone'                 => '00000000',
            'address'               => 'Dirección 0',
            'country_id'            => factory(Country::class),
            'currency_id'           => factory(Currency::class)->create(),
            'allow_add_products'    => 1,
            'allow_add_stores'      => 1,
            'is_electronic_invoice' => 1,
            'uses_fel'              => 1,
            'deleted_at'            => now(),
        ]);

        factory(Company::class, 5)->create();
    }
}
