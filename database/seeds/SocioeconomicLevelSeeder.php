<?php

use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;
use Illuminate\Database\Seeder;

class SocioeconomicLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(SocioeconomicLevel::class, 3)->create();
    }
}
