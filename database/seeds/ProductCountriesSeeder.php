<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\ProductCountries\ProductCountries;
class ProductCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductCountries::class, 3)->create();
    }
}
