<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Support\Arr;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\Adjustment\Adjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdjustmentControllerTest extends ApiTestCase
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
  public function a_guest_cannot_access_to_adjustment_resources()
  {
    $this->getJson(route('adjustments.index'))->assertUnauthorized();
    $this->postJson(route('adjustments.store'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_adjustment_resources()
  {
    $this->signIn();
    
    $this->getJson(route('adjustments.index'))->assertForbidden();
    $this->postJson(route('adjustments.store'))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_adjustments()
  {
    $user = $this->signInWithPermissionsTo(['adjustments.index']);

    $store = Store::has('products')->first();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $products = [
      [
        'id'       => $store->products->first()->id,
        'quantity' => 10,
      ],
      ['id'       => $store->products->last()->id,
        'quantity' => 20,
      ],
    ];
        
    $values = [
      'store_id'       => $store->id,
      'origin_type'    => StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT,
      'description'    => 'Test Adjustment',
      'products'       => $products,
    ];

    Adjustment::create($values);

    $this->getJson(route('adjustments.index', ['store_id' => $store->id]))
      ->assertOk()
      ->assertJsonFragment([
        'store_name'  => $store->name,
        'user_name'   => $user->name,
        'description' => 'Test Adjustment',
      ]);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_adjustment()
  {
    $user = $this->signInWithPermissionsTo(['adjustments.store']);

    $store = Store::has('products')->first();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $productsAttributes = [];

    foreach ($store->products as $product) {
      $productsAttributes[] = [
        'id'       => $product->id,
        'quantity' => rand(150, 200),
        'stock'    => $product->pivot->quantity,
      ];
    }

    $attributes = [
      'store_id'    => $store->id,
      'description' => 'Un Ajuste de Prueba',
      'products'    => $productsAttributes,
    ];

    $this->postJson(route('adjustments.store'), $attributes)
      ->assertCreated();

    $this->assertDatabaseHas('stock_movements', Arr::except($attributes, ['products',]));

    foreach ($productsAttributes as $product) {
      $this->assertDatabaseHas('stock_stores', [
        'store_id'   => $store->id,
        'product_id' => $product['id'],
        'quantity'   => $product['quantity'],
      ]);

      $this->assertDatabaseHas('stock_movements_detail', [
        'stock_movement_id' => $store->stockMovements->where('origin_type', StockMovement::OPTION_ORIGIN_TYPE_MANUAL_ADJUSTMENT)->first()->id,
        'stock_store_id'    => $store->products()->wherePivot('product_id', $product['id'])->first()->pivot->id,
        'product_id'        => $product['id'],
        'quantity'          => $product['quantity'] - $product['stock'],
      ]);
    }
  }
}