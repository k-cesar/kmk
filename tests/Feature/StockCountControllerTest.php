<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Product\Product;
use App\Http\Modules\StockCount\StockCount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockCountControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
    $this->seed(['PermissionSeeder', 'StockCountSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_stock_counts_resources()
  {
    $this->getJson(route('stock-counts.index'))->assertUnauthorized();
    $this->getJson(route('stock-counts.show', rand()))->assertUnauthorized();
    $this->postJson(route('stock-counts.store'))->assertUnauthorized();
    $this->deleteJson(route('stock-counts.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_stock_counts_resources()
  {
    $this->signIn();
    
    $randomStockCountID = StockCount::all()->random()->id;

    $this->getJson(route('stock-counts.index'))->assertForbidden();
    $this->getJson(route('stock-counts.show', $randomStockCountID))->assertForbidden();
    $this->postJson(route('stock-counts.store'))->assertForbidden();
    $this->deleteJson(route('stock-counts.destroy', $randomStockCountID))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_stock_counts()
  {
    $user = $this->signInWithPermissionsTo(['stock-counts.index']);

    $store = StockCount::all()->first()->store;

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $response = $this->getJson(route('stock-counts.index', ['store_id' => $store->id]))
      ->assertOk();
      
    foreach (StockCount::limit(10)->get() as $stockCount) {
      $response->assertJsonFragment($stockCount->toArray());
    }
  }

    /**
   * @test
   */
    public function an_user_with_permission_can_see_a_stock_counts()
    {
      $user = $this->signInWithPermissionsTo(['stock-counts.show']);

      $stockCount = factory(StockCount::class)->create();

      if ($user->role->level > 1) {
        if ($user->role->level == 2) {
          $user->update(['company_id' => $stockCount->store()->first()->company_id]);
        } else {
          $user->stores()->sync($stockCount->store_id);
        }
      }

      $this->getJson(route('stock-counts.show', $stockCount->id))
        ->assertOk();
    }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_stock_counts()
  {
    $user = $this->signInWithPermissionsTo(['stock-counts.store']);

    $store = factory(Store::class)->create();
    $products = factory(Product::class, 2)->create();

    $store->products()->attach($products->pluck('id'));

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $attributes = factory(StockCount::class)->raw([
      'store_id'   => $store->id,
      'created_by' => $user->id,
      'count_date' => now()->format('Y-m-d'),
      'status'     => StockCount::OPTION_STATUS_OPEN,
    ]);

    $productsAttributes = [
      'products' => [
        [
          'id'         => $products->first()->id,
          'quantity'   => 10,
        ],
        [
          'id'         => $products->last()->id,
          'quantity'   => 20,
        ]
      ]
    ];

    $this->postJson(route('stock-counts.store'), array_merge($attributes, $productsAttributes))
        ->assertCreated();
    
    $this->assertDatabaseHas('stock_counts', $attributes);

    $this->assertDatabaseHas('stock_counts_detail', [
      'product_id' => $products->first()->id,
      'quantity'   => 10
    ]);

    $this->assertDatabaseHas('stock_counts_detail', [
      'product_id' => $products->last()->id,
      'quantity'   => 20
    ]);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_stock_counts()
  {
    $user = $this->signInWithPermissionsTo(['stock-counts.destroy']);

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $stockCount = factory(StockCount::class)->create(['store_id' => $store->id, 'status' => StockCount::OPTION_STATUS_OPEN]);

    $this->deleteJson(route('stock-counts.destroy', $stockCount))
        ->assertOk();
    
    $this->assertDatabaseHas('stock_counts', [
      'id'     => $stockCount->id,
      'status' => StockCount::OPTION_STATUS_CANCELLED
    ]);
  }

}
