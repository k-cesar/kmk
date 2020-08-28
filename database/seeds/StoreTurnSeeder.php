<?php

use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Database\Seeder;

class StoreTurnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StoreTurn::class, 3)->create();
    }
}
