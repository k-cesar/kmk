<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            // UserSeeder::class,
            // CompanySeeder::class,
            // CurrencySeeder::class,
            // LocationSeeder::class,
        ]);
    }
}
