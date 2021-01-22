<?php

use App\Http\Modules\Uom\Uom;
use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Uom::class, 3)->create();
    }
}
