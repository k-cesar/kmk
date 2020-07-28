<?php

use App\Http\Modules\ProductSubCategories\ProductSubCategories;
use Illuminate\Database\Seeder;

class ProductSubCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductSubCategories::class, 10)->create();
    }
}
