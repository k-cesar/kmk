<?php

use Illuminate\Database\Seeder;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Product\Product;

class StockStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Store::limit(2)->get() as $store) {
            $products = [];
            
            foreach (Product::limit(2)->get() as $product){
                $products[$product->id] = ['quantity' => 100];
            }
            
            $store->products()->sync($products);
        }
    }
}
