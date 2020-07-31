<?php

use App\Http\Modules\Presentations\Presentations;
use Illuminate\Database\Seeder;

class PresentationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Presentations::class, 5)->create();
    }
}
