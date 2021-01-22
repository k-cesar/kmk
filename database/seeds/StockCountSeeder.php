<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\StockCount\StockCount;

class StockCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StockCount::class, 3)->create();
    }
}
