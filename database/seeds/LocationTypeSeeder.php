<?php

use App\Http\Modules\LocationType\LocationType;
use Illuminate\Database\Seeder;

class LocationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(LocationType::class, 3)->create();
    }
}
