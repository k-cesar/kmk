<?php

use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Deposit\DepositImage;
use Illuminate\Database\Seeder;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Deposit::class, 3)->create()
           ->each(function (Deposit $deposit) {
                factory(DepositImage::class, 3)->create(['deposit_id' => $deposit->id]);
            });
    }
}
