<?php

use App\Http\Modules\Uom\Uom;
use Illuminate\Database\Seeder;
use App\Http\Modules\Product\Product;
use App\Http\Modules\Stock\StockStore;
use App\Http\Modules\Purchase\Purchase;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\Purchase\PurchaseDetail;
use App\Http\Modules\Stock\StockMovementDetail;

class PurchaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $uom  = Uom::all()->first() ?? factory(Uom::class)->create();
    $productsByPurchase = 4;

    for ($i=0; $i < 2; $i++) { 
        
      $stockMovement = factory(StockMovement::class)->create([
        'origin_type' => StockMovement::OPTION_ORIGIN_TYPE_PURCHASE,
      ]);
          
      $products = factory(Product::class, $productsByPurchase)->create(['uom_id' => $uom]);

      $products->each(function ($product, $item_line) use ($productsByPurchase, $stockMovement){

        $quantity = rand(1, 10);

        $purchase = Purchase::find($stockMovement->origin_id);

        PurchaseDetail::create([
          'item_line'   => $item_line,
          'purchase_id' => $purchase->id,
          'product_id'  => $product->id,
          'quantity'    => $quantity,
          'unit_price'  => $purchase->total/($quantity * $productsByPurchase),
        ]);

        $stockStore = StockStore::create([
          'store_id'   => $stockMovement->store->id,
          'product_id' => $product->id,
          'quantity'   => $quantity
        ]);

        StockMovementDetail::create([
          'stock_movement_id' => $stockMovement->id,
          'stock_store_id'    => $stockStore->id,
          'product_id'        => $product->id,
          'quantity'          => $quantity,
        ]);

      });
    }
  }
}
