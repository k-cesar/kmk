<?php

use App\Http\Modules\Sell\Sell;
use Illuminate\Database\Seeder;
use App\Http\Modules\Sell\SellInvoice;
use App\Http\Modules\SellPayment\SellPayment;

class SellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sell = factory(Sell::class)->create();

        factory(SellInvoice::class)->create([
            'sell_id' => $sell->id,
        ]);

        factory(SellPayment::class)->create([
            'sell_id' => $sell->id,
        ]);
    }
}
