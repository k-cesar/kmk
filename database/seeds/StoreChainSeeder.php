<?php

use App\Http\Modules\StoreChain\StoreChain;
use Illuminate\Database\Seeder;

class StoreChainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StoreChain::class, 5)->create();
    }
}
