<?php

namespace App\Http\Modules\Adjustment;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Modules\Stock\StockStore;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\StockCount\StockCount;
use App\Http\Modules\Stock\StockMovementDetail;

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
      $originId = StockCount::find($values['stock_count_id'])->id;
    }

    try {
      DB::beginTransaction();

      $stockMovementValues = [
        'user_id'       => auth()->user()->id,
        'origin_id'     => $originId,
        'origin_type'   => $values['origin_type'],
        'date'          => now(),
        'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_ADJUSTMENT,
        'store_id'      => $values['store_id'],
        'description'   => $values['description'],
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
  * @param App\Http\Modules\StockCount\StockCount
  * 
  * @return boolean
  */
  
  public static function createFromStockCount(StockCount $stockCount)
  {
    $products = $stockCount->stockCountDetails->map(function ($stockCountDetail) {
      return [
        'id'       => $stockCountDetail->product_id,
        'quantity' => $stockCountDetail->quantity,
      ];
    });

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