<?php

use App\Http\Modules\PaymentMethod\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(PaymentMethod::class, 2)->create();
    }
}
