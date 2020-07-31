<?php

use App\Http\Modules\ProductPresentation\ProductPresentation;
use Illuminate\Database\Seeder;

class ProductPresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductPresentation::class, 2)->create();
    }
}
