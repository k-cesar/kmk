<?php

use App\Http\Modules\Zone\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Zone::class, 5)->create();
    }
}
