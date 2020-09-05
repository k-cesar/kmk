<?php

use App\Http\Modules\PresentationSku\PresentationSku;
use Illuminate\Database\Seeder;

class PresentationSkuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(PresentationSku::class, 5)->create();
    }
}
