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
            ProductDepartmentSeeder::class,
            ProductCategorySeeder::class,
            ProductSubcategorySeeder::class,
            ProductSeeder::class,
            ProductCountriesSeeder::class,
            PresentationSeeder::class,
            StoreFormatSeeder::class,
            StoreChainSeeder::class,
            StoreFlagSeeder::class,
            RegionSeeder::class,
            StateSeeder::class,
            MunicipalitySeeder::class,
            ZoneSeeder::class,
            StoreSeeder::class,
            ClientSeeder::class,
            TurnSeeder::class,
            PresentationSkuSeeder::class,
            PresentationComboSeeder::class,
            StockStoreSeeder::class,
            PurchaseSeeder::class,
            StockCountsSeeder::class,
            StockCountsDetailSeeder::class,
            SellSeeder::class,
            StoreTurnSeeder::class,
        ]);
    }
}
