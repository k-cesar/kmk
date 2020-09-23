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
            UserSeeder::class,
            MakerSeeder::class,
            ProviderSeeder::class,
            SocioeconomicLevelSeeder::class,
            LocationTypeSeeder::class,
            RegionSeeder::class,
            StateSeeder::class,
            MunicipalitySeeder::class,
            ZoneSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
