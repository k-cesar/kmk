<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\StockCountsDetail\StockCountsDetail;

class StockCountsDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StockCountsDetail::class, 5)->create();
    }
}
