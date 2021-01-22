<?php

use App\Http\Modules\Presentation\Presentation;
use Illuminate\Database\Seeder;

class PresentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Presentation::class, 3)->create();
    }
}
