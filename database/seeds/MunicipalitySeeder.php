<?php

use App\Http\Modules\Municipality\Municipality;
use Illuminate\Database\Seeder;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Municipality::class, 2)->create();
    }
}
