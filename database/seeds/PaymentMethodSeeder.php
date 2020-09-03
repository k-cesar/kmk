<?php

use App\Http\Modules\Company\Company;
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
        $paymentMethods = [
            PaymentMethod::OPTION_PAYMENT_CASH,
            PaymentMethod::OPTION_PAYMENT_CARD,
            PaymentMethod::OPTION_PAYMENT_CREDIT,
        ];

        foreach ($paymentMethods as $paymentMethod) {
            factory(PaymentMethod::class)->create([
                'name'       => $paymentMethod,
                'company_id' => 0,
            ]);
        }
    }
}
