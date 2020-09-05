<?php

namespace App\Http\Modules\Adjustment;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\Stock\StockMovementDetail;
use App\Http\Modules\Stock\StockStore;
use App\Http\Modules\StoreTurn\StoreTurn;
use App\Http\Modules\StockCounts\StockCounts;

class Adjustment
{
  /**
   * Create and store an Adjustment
   *
   * @param array $values
   * 
   * @return boolean
   */
  public static function create(array $values)
  {
    if ($values['origin_type']==StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT) {
      $originId = DB::table('origin_sequence')->insertGetId([]);
    } elseif ($values['origin_type']==StockMovement::OPTION_ORIGIN_TYPE_COUNT) {
      $originId = StockCounts::find($values['stock_count_id'])->id;
    }

    try {
      DB::beginTransaction();

      $stockMovementValues = [
        'date'          => now(),
        'description'   => $values['description'],
        'origin_type'   => $values['origin_type'],
        'origin_id'     => $originId,
        'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_ADJUSTMENT,
        'store_id'      => $values['store_id'],
        'user_id'       => auth()->user()->id,
      ];

      $stockMovement = StockMovement::create($stockMovementValues);

      foreach ($values['products'] as $product) {
        $stockStore = $stockMovement->store
          ->products()
          ->wherePivot('product_id', $product['id'])
          ->first()
          ->pivot;
          
        StockMovementDetail::create([
          'stock_movement_id' => $stockMovement->id,
          'stock_store_id'    => $stockStore->id,
          'product_id'        => $product['id'],
          'quantity'          => $product['quantity'] - $stockStore->quantity,
        ]);

        $stockStore->quantity = $product['quantity'];
        $stockStore->save();
      }

      DB::commit();

      return true;

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return false;
    }
  }

  /**
  * Store an Adjustment created from StockCount given.
  * 
  * @param App\Http\Modules\StockCounts\StockCounts
  * 
  * @return boolean
  */
  
  public static function createFromStockCount(StockCounts $stockCount)
  {
    $products = [];

    $stockCountsDetails = DB::table('stock_counts_detail')
      ->where('stock_count_id', $stockCount->id)
      ->get();

    foreach ($stockCountsDetails as $stockCountDetail) {
      
      $stockStore = StockStore::where('store_id', $stockCount->store_id)
        ->where('product_id', $stockCountDetail->product_id)
        ->first();
      
      $diff = $stockCountDetail->quantity - $stockStore->quantity;

      if ($diff) {
        $product = [
          'id'       => $stockCountDetail->product_id,
          'quantity' => $stockCountDetail->quantity,
        ];

        $products[$product['id']] = $product;
      }
    }

    $values = [
      'stock_count_id' => $stockCount->id,
      'store_id'       => $stockCount->store_id,
      'origin_type'    => StockMovement::OPTION_ORIGIN_TYPE_COUNT,
      'description'    => 'Generado por Conteo.',
      'products'       => $products,
    ];

    return self::create($values);
  }
}