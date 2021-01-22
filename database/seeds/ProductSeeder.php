<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Product\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Product::class, 3)->create();
    }
}
