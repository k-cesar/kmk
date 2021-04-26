<?php

namespace App\Http\Modules\Store;

use Exception;
use App\Http\Modules\Store\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StoreDuplicateController extends Controller
{
  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Store\StoreRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StoreDuplicateRequest $request)
  {
    $this->authorize('create', Store::class);

    try {
      DB::beginTransaction();

      $storeDuplicated = Store::create($request->validated());

      $storeDuplicated->copyTurnsAndPricesFrom(Store::find($request->store_id));

      DB::commit();
      
      return $this->showOne($storeDuplicated, 201);

    } catch (Exception $exception) {
      DB::rollback();

      Log::error($exception);

      return $this->errorResponse(500, "Ha ocurrido un error interno");
    }
  }
}
