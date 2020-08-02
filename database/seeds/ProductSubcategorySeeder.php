<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\ProductSubcategory\ProductSubcategory;

class ProductSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductSubcategory::class, 10)->create();
    }
}
