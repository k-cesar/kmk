<?php

use App\Http\Modules\Sell\Sell;
use App\Http\Modules\SellPayment\SellPayment;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Seeder;

class SellPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);
        
        $sell = factory(Sell::class)->create([
            'store_id'      => $storeTurn->store_id,
            'store_turn_id' => $storeTurn->id,
            'status'        => Sell::OPTION_STATUS_PENDING
        ]);

        factory(SellPayment::class)->create(['sell_id' => $sell->id]);

        factory(SellPayment::class, 2)->create();
    }
}
