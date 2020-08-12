<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\StockCounts\StockCounts;

class StockCountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StockCounts::class, 5)->create();
    }
}
