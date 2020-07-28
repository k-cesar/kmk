<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Products\Products;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Products::class, 2)->create();
    }
}
