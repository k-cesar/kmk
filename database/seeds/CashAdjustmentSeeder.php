<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\CashAdjustment\CashAdjustment;

class CashAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CashAdjustment::class, 5)->create();
    }
}
