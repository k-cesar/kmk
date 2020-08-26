<?php

namespace Tests\Feature;

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

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreSeeder', 'ProductSeeder', 'StockStoreSeeder']);
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

    $productA = $storeOutput->products->first();
    $productB = $storeOutput->products->last();

    $stockStoreOutputBeforeTransfer = [
      $productA->id => $productA->pivot,
      $productB->id => $productB->pivot,
    ];
    
    $stockStoreIntputBeforeTransfer = [
      $productA->id => $storeInput->products->where('id', $productA->id)->first()->pivot,
      $productB->id => $storeInput->products->where('id', $productB->id)->first()->pivot,
    ];

    $attributes = [
      'origin_store_id'  => $storeOutput->id,
      'destiny_store_id' => $storeInput->id,
      'products'         => [
        [
          'id'       => $productA->id,
          'quantity' => 5,
        ],
        [
          'id'       => $productB->id,
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

    foreach ($attributes['products'] as $product) {

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_store_id' => $stockStoreOutputBeforeTransfer[$product['id']]->id,
        'product_id'     => $product['id'],
        'quantity'       => -1 * $product['quantity'],
      ]);

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_store_id' => $stockStoreIntputBeforeTransfer[$product['id']]->id,
        'product_id'     => $product['id'],
        'quantity'       => $product['quantity'],
      ]);

      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $storeOutput->id,
        'product_id' => $product['id'],
        'quantity'   => $stockStoreOutputBeforeTransfer[$product['id']]->quantity - $product['quantity'],
      ]);

      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $storeInput->id,
        'product_id' => $product['id'],
        'quantity'   => $stockStoreOutputBeforeTransfer[$product['id']]->quantity + $product['quantity'],
      ]);
    }

    $storeInput = Store::whereDoesntHave('products')->first();

    $attributes = [
      'origin_store_id'  => $storeOutput->id,
      'destiny_store_id' => $storeInput->id,
      'products'         => [
        [
          'id'       => $productA->id,
          'quantity' => 15,
        ]
      ],
    ];

    $this->postJson(route('transfers.store'), $attributes)
      ->assertCreated();

    $product = $attributes['products'][0];

    $this->assertDatabaseHas('stock_movements_detail', [
      'stock_store_id' => $storeInput->products->where('id', $product['id'])->first()->pivot->id,
      'product_id'     => $product['id'],
      'quantity'       => $product['quantity'],
    ]);

    $this->assertDatabaseHas('stock_stores', [
      'store_id'   => $storeInput->id,
      'product_id' => $product['id'],
      'quantity'   => $product['quantity'],
    ]);
  }
}