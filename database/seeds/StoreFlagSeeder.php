<?php

use App\Http\Modules\StoreFlag\StoreFlag;
use Illuminate\Database\Seeder;

class StoreFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StoreFlag::class, 3)->create();
    }
}
