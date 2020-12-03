<?php

use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\Uom\Uom;
use Illuminate\Database\Seeder;
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
    $presentationsByPurchase = 4;

    for ($i=0; $i < 2; $i++) { 
        
      $stockMovement = factory(StockMovement::class)->create([
        'origin_type' => StockMovement::OPTION_ORIGIN_TYPE_PURCHASE,
      ]);
          
      $presentations = factory(Presentation::class, $presentationsByPurchase)->create();

      $presentations->each(function ($presentation, $item_line) use ($presentationsByPurchase, $stockMovement){

        $purchase = Purchase::find($stockMovement->origin_id);

        $quantity  = rand(1, 10);
        $unitPrice = $purchase->total/($quantity * $presentationsByPurchase);
        $total     = $quantity * $unitPrice;

        PurchaseDetail::create([
          'item_line'       => $item_line,
          'purchase_id'     => $purchase->id,
          'presentation_id' => $presentation->id,
          'quantity'        => $quantity,
          'unit_price'      => $unitPrice,
          'total'           => $total,
        ]);

        $stockStore = StockStore::create([
          'store_id'   => $stockMovement->store->id,
          'product_id' => $presentation->product_id,
          'quantity'   => $quantity * $presentation->units,
        ]);

        StockMovementDetail::create([
          'stock_movement_id' => $stockMovement->id,
          'stock_store_id'    => $stockStore->id,
          'product_id'        => $presentation->product_id,
          'quantity'          => $quantity * $presentation->units,
        ]);

      });
    }
  }
}
