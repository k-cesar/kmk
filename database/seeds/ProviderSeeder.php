<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Provider\Provider;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Provider::class, 3)->create();
    }
}
