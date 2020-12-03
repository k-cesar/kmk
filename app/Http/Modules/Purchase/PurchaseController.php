<?php

namespace App\Http\Modules\Purchase;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Modules\Stock\StockStore;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Support\Facades\Validator;
use App\Http\Modules\Presentation\Presentation;
use App\Http\Modules\Stock\StockMovementDetail;

class PurchaseController extends Controller
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
    
    $purchases = Purchase::query()
      ->with('store:id,name')
      ->with('user:id,name')
      ->with('provider:id,name')
      ->whereHas('user', function ($query) {
        return $query->select('id', 'name')
          ->whereRaw('LOWER(name) LIKE ?', [strtolower(request()->query('user_name', '%'))]);
      })
      ->whereHas('provider', function ($query) {
        return $query->select('id', 'name')
          ->whereRaw('LOWER(name) LIKE ?', [strtolower(request()->query('provider_name', '%'))]);
      })
      ->where('store_id', request('store_id'));

    return $this->showAll($purchases, Arr::except(Schema::getColumnListing((new Purchase)->getTable()), 1));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Purchase\PurchaseRequest  $request
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(PurchaseRequest $request)
  {
    try {
      DB::beginTransaction();

      $presentations = collect($request->validated()['presentations'])
        ->map(function ($presentation) {
          $presentation['total'] = $presentation['quantity'] * $presentation['unit_price'];
          return $presentation;
        });
      
      $total = $presentations->pluck('total')->sum();

      $date = now();

      $purchase = Purchase::create(array_merge($request->validated(),[
        'user_id' => auth()->user()->id,
        'date'    => $date,
        'total'   => $total
      ]));

      $stockMovement = StockMovement::create([
        'user_id'       => auth()->user()->id,
        'origin_id'     => $purchase->id,
        'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_PURCHASE,
        'date'          => $date,
        'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_INPUT,
        'store_id'      => $purchase->store_id,
      ]);

      $presentations->each(function ($presentation, $item_line) use ($purchase, $stockMovement) {

        PurchaseDetail::create(array_merge($presentation, [
          'total'           => $presentation['quantity'] * $presentation['unit_price'],
          'item_line'       => $item_line,
          'purchase_id'     => $purchase->id,
          'presentation_id' => $presentation['id'],
        ]));

        $presentationStored = Presentation::find($presentation['id']);

        $stockStore = StockStore::firstOrCreate([
          'store_id'   => $purchase->store->id,
          'product_id' => $presentationStored->product_id,
        ]);

        $stockStore->quantity += $presentation['quantity'] * $presentationStored->units;
        $stockStore->save();

        StockMovementDetail::create([
          'stock_movement_id' => $stockMovement->id,
          'stock_store_id'    => $stockStore->id,
          'product_id'        => $presentationStored->product_id,
          'quantity'          => $presentation['quantity'] * $presentationStored->units,
        ]);

      });
      
      DB::commit();

      return $this->showOne($purchase, 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, 'Ha ocurrido un error interno');
    }
    
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Purchase\Purchase  $maker
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Purchase $purchase)
  {
    $purchase->load('store:id,name', 
      'user:id,name', 
      'provider:id,name', 
      'paymentMethod:id,name',
      'purchaseDetails.presentation:id,description'
    );

    return $this->showOne($purchase);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Purchase\PurchaseRequest  $request
   * @param  App\Http\Modules\Purchase\Purchase  $purchase
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(PurchaseRequest $request, Purchase $purchase)
  {
    $purchase->update($request->validated());

    return $this->showOne($purchase);
  }
}
