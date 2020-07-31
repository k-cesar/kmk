<?php

use App\Http\Modules\PresentationCombo\PresentationCombo;
use Illuminate\Database\Seeder;

class PresentationComboSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(PresentationCombo::class, 2)->create();
    }
}
