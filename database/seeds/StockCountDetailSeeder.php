<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\StockCount\StockCountDetail;

class StockCountDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StockCountDetail::class, 5)->create();
    }
}
