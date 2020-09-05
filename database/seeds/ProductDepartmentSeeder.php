<?php

use App\Http\Modules\ProductDepartment\ProductDepartment;
use Illuminate\Database\Seeder;

class ProductDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ProductDepartment::class, 5)->create();
    }
}
