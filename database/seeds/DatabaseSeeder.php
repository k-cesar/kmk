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
            MakerSeeder::class,
            BrandSeeder::class,
            ProviderSeeder::class,
            PaymentMethodSeeder::class,
            UomSeeder::class,
            SocioeconomicLevelSeeder::class,
            LocationTypeSeeder::class,
            StoreTypeSeeder::class,
        ]);
    }
}
