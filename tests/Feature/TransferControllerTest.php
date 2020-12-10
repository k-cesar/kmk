<?php

namespace Tests\Feature;

use App\Http\Modules\Presentation\Presentation;
use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Support\Facades\DB;
use App\Http\Modules\Stock\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'StoreSeeder', 'ProductSeeder', 'StockStoreSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_transfer_resources()
  {
    $this->getJson(route('transfers.index'))->assertUnauthorized();
    $this->postJson(route('transfers.store'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_transfer_resources()
  {
    $this->signIn();
    
    $this->getJson(route('transfers.index'))->assertForbidden();
    $this->postJson(route('transfers.store'))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_transfers()
  {
    $user = $this->signInWithPermissionsTo(['transfers.index']);

    $storeOutput = Store::first();
    $storeInput = Store::all()->last();
    $originId = DB::table('origin_sequence')->insertGetId([]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeOutput->company_id]);
      } else {
        $user->stores()->sync(Store::all()->pluck('id'));
      }
    }

    $stockMovement = [
      'user_id'       => $user->id,
      'origin_id'     => $originId,
      'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_TRANSFER,
      'date'          => now(),
      'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT,
      'store_id'      => $storeOutput->id,
    ];

    DB::table('stock_movements')->insert($stockMovement);

    $stockMovement['movement_type'] = StockMovement::OPTION_MOVEMENT_TYPE_INPUT;
    $stockMovement['store_id'] = $storeInput->id;

    DB::table('stock_movements')->insert($stockMovement);

    $response = $this->getJson(route('transfers.index', ['store_id' => $storeOutput->id]))
      ->assertOk();
  
    $response->assertJsonFragment([
      'origin_store_id'    => "{$storeOutput->id}",
      'origin_store_name'  => $storeOutput->name,
      'destiny_store_id'   => "{$storeInput->id}",
      'destiny_store_name' => $storeInput->name,
      'user_name'          => $user->name
    ]);
      
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_transfer()
  {
    $user = $this->signInWithPermissionsTo(['transfers.store']);

    $storeOutput = Store::has('products')->first();
    $storeInput  = Store::has('products')->where('id', '!=', $storeOutput->id)->first();
    $storeInput->update(['company_id' => $storeOutput->company_id]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeOutput->company_id]);
      } else {
        $user->stores()->sync(Store::all()->pluck('id'));
      }
    }

    $productA = $storeOutput->products->first();
    $productB = $storeOutput->products->last();

    $presentationA = factory(Presentation::class)->create(['product_id' => $productA->id, 'units' => 1 ]);
    $presentationB = factory(Presentation::class)->create(['product_id' => $productB->id, 'units' => 6 ]);

    $stockStoreOutputBeforeTransfer = [
      $productA->id => $productA->pivot,
      $productB->id => $productB->pivot,
    ];
    
    $stockStoreInputBeforeTransfer = [
      $productA->id => $storeInput->products->where('id', $productA->id)->first()->pivot,
      $productB->id => $storeInput->products->where('id', $productB->id)->first()->pivot,
    ];

    $attributes = [
      'origin_store_id'  => $storeOutput->id,
      'destiny_store_id' => $storeInput->id,
      'presentations'    => [
        [
          'id'       => $presentationA->id,
          'quantity' => 5,
        ],
        [
          'id'       => $presentationB->id,
          'quantity' => 10,
        ]
      ],
    ];

    $this->postJson(route('transfers.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('stock_movements', [
      'user_id'       => $user->id,
      'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_TRANSFER,
      'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_OUTPUT,
      'store_id'      => $storeOutput->id,
    ]);

    $this->assertDatabaseHas('stock_movements', [
      'user_id'       => $user->id,
      'origin_type'   => StockMovement::OPTION_ORIGIN_TYPE_TRANSFER,
      'movement_type' => StockMovement::OPTION_MOVEMENT_TYPE_INPUT,
      'store_id'      => $storeInput->id,
    ]);

    foreach ($attributes['presentations'] as $presentation) {

      $presentationStored = Presentation::where('id', $presentation['id'])->first();

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_store_id' => $stockStoreOutputBeforeTransfer[$presentationStored->product_id]->id,
        'product_id'     => $presentationStored->product_id,
        'quantity'       => -1 * $presentation['quantity'] * $presentationStored->units,
      ]);

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_store_id' => $stockStoreInputBeforeTransfer[$presentationStored->product_id]->id,
        'product_id'     => $presentationStored->product_id,
        'quantity'       => $presentation['quantity'] * $presentationStored->units,
      ]);

      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $storeOutput->id,
        'product_id' => $presentationStored->product_id,
        'quantity'   => $stockStoreOutputBeforeTransfer[$presentationStored->product_id]->quantity - ($presentation['quantity'] * $presentationStored->units),
      ]);

      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $storeInput->id,
        'product_id' => $presentationStored->product_id,
        'quantity'   => $stockStoreInputBeforeTransfer[$presentationStored->product_id]->quantity + ($presentation['quantity'] * $presentationStored->units),
      ]);
    }

    $storeInput = Store::whereDoesntHave('products')->first();
    $storeInput->update(['company_id' => $storeOutput->company_id]);

    $attributes = [
      'origin_store_id'  => $storeOutput->id,
      'destiny_store_id' => $storeInput->id,
      'presentations'    => [
        [
          'id'       => $presentationA->id,
          'quantity' => 15,
        ]
      ],
    ];

    $this->postJson(route('transfers.store'), $attributes)
      ->assertCreated();

    $presentation = $attributes['presentations'][0];
    $presentationStored = Presentation::where('id', $presentation['id'])->first();

    $this->assertDatabaseHas('stock_movements_detail', [
      'stock_store_id' => $storeInput->products->where('id', $presentationStored->product_id)->first()->pivot->id,
      'product_id'     => $presentationStored->product_id,
      'quantity'       => $presentation['quantity'] * $presentationStored->units,
    ]);

    $this->assertDatabaseHas('stock_stores', [
      'store_id'   => $storeInput->id,
      'product_id' => $presentationStored->product_id,
      'quantity'   => $presentation['quantity'] * $presentationStored->units,
    ]);
  }
}