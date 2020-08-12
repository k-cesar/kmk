<?php

namespace App\Http\Modules\Transfer;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\StockMovement\StockMovement;

class TransferController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    Validator::validate(request()->all(), [
      'store_id' => 'required|exists:stores,id',
    ]);

    $transfers = DB::table('stock_movements as smo')
      ->select('so.name as origin_store_name', 'sd.name as destiny_store_name', 'smo.date', 'u.name as user_name')
      ->join('stock_movements as smd','smo.origin_id', '=', 'smd.origin_id')
      ->join('stores as so', 'smo.store_id', '=', 'so.id')
      ->join('stores as sd', 'smd.store_id', '=', 'sd.id')
      ->join('users as u', 'smo.user_id', '=', 'u.id')
      ->where('smo.movement_type', '=', StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT)
      ->where('smd.movement_type', '=', StockMovement::OPTION_MOVEMENT_TYPE_INPUT)
      ->where('smo.origin_type', '=', StockMovement::OPTION_ORIGIN_TYPE_TRANSFER)
      ->where('smo.store_id', '=', request()->store_id)
      ->where('smo.date', 'like', request()->query('date', '%'))
      ->whereRaw('LOWER(sd.name) LIKE ?', [strtolower(request()->query('destiny_store_name', '%'))])
      ->whereRaw('LOWER(u.name) LIKE ?', [strtolower(request()->query('user_name', '%'))]);
      
    
    return $this->showAll($transfers);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Transfer\TransferRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(TransferRequest $request)
  {
    $transfer = $request->validated();
    $originId = DB::table('origin_sequence')->insertGetId([]);

    try {
      DB::beginTransaction();

      $stockMovement = [
        'user_id'       => auth()->user()->id,
        'origin_id'     => $originId,
        'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_TRANSFER,
        'date'          => now(),
        'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT,
        'store_id'      => $transfer['origin_store_id'],
      ];

      // Creando el movimeinto de salida
      $stockMovementOutput = StockMovement::create($stockMovement);

      $stockMovement['movement_type'] = StockMovement::OPTION_MOVEMENT_TYPE_INPUT;
      $stockMovement['store_id'] = $transfer['destiny_store_id'] ;

      // Creando el movimeinto de entrada
      $stockMovementInput = StockMovement::create($stockMovement);

      foreach ($transfer['products'] as $product) {
        // Obteniendo el inventario de salida
        $stockStoreOutput = $stockMovementOutput->store
          ->products()
          ->wherePivot('product_id', $product['id'])
          ->first()
          ->pivot;

        // Actualizando el inventario de salida
        $stockStoreOutput->quantity -= $product['quantity'];
        $stockStoreOutput->save();

        // Creando el detalle del movimiento de salida
        DB::table('stock_movements_detail')->insert([
          'stock_movement_id' => $stockMovementOutput->id,
          'stock_store_id'    => $stockStoreOutput->id,
          'product_id'        => $product['id'],
          'quantity'          => -1 * $product['quantity'],
          'created_at'        => now(),
          'updated_at'        => now(),
        ]);

        // Obteniendo el inventario de entrada
        $productInput = $stockMovementInput->store
          ->products()
          ->wherePivot('product_id', $product['id'])
          ->first();

        if ($productInput) {
          // Actualizando el inventario de salida
          $stockStoreInput = $productInput->pivot;
          $stockStoreInput->quantity += $product['quantity'];
          $stockStoreInput->save();
          $stockStoreInputId = $stockStoreInput->id;

        } else {
          // Creando el inventario de salida
          $stockStoreInputId = DB::table('stock_stores')->insertGetId([
            'store_id'   => $stockMovementInput->store->id,
            'product_id' => $product['id'],
            'quantity'   => $product['quantity'],
            'created_at'        => now(),
            'updated_at'        => now(),
          ]);
        }
        
        // Creando el detalle del movimiento de salida
        DB::table('stock_movements_detail')->insert([
          'stock_movement_id' => $stockMovementInput->id,
          'stock_store_id'    => $stockStoreInputId,
          'product_id'        => $product['id'],
          'quantity'          => $product['quantity'],
          'created_at'        => now(),
          'updated_at'        => now(),
        ]);
      }
      
      DB::commit();

      return $this->showMessage('Transferencia exitosa', 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, 'Ha ocurrido un error interno');
    }
  }
}
