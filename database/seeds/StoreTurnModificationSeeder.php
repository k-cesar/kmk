<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\StoreTurnModification\StoreTurnModification;

class StoreTurnModificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(StoreTurnModification::class, 5)->create();
    }
}
