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
            CurrencySeeder::class,
            CountrySeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            ProductDepartmentSeeder::class
        ]);
    }
}
