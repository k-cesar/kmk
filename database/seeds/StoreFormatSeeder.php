<?php

use App\Http\Modules\StoreFormat\StoreFormat;
use Illuminate\Database\Seeder;

class StoreFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StoreFormat::class, 2)->create();
    }
}
