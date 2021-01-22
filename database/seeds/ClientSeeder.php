<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Client\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        factory(Client::class, 3)->create();
    }
}
