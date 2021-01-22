<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Company\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Company::class, 3)->create();
    }
}
