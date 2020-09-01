<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Stock\StockMovement;
use App\Http\Modules\StockCounts\StockCounts;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;

class StockCountAdjustmentControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StoreSeeder', 'ProductSeeder', 'StockStoreSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_stock_count_adjustment_resources()
  {
    $this->postJson(route('adjustments.store'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_stock_count_adjustment_resources()
  {
    $this->signIn();
    
    $this->postJson(route('adjustments.store'))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_stock_count_adjustment()
  {
    $user = $this->signInWithPermissionsTo(['stock-counts-adjustments.store']);

    $store = Store::has('products')->first();

    $stockCount = factory(StockCounts::class)->create([
      'store_id' => $store->id,
      'status'   => StockCounts::OPTION_STATUS_CLOSED,
    ]);

    foreach ($store->products as $product) {
      DB::table('stock_counts_detail')->insert([
        'stock_count_id' => $stockCount->id,
        'product_id'     => $product->id,
        'quantity'       => rand(150, 200)
      ]);
    }

    $attributes = [
      'store_id'       => $store->id,
      'stock_count_id' => $stockCount->id,
    ];

    $this->postJson(route('stock-counts-adjustments.store'), $attributes)
      ->assertCreated();

    $this->assertDatabaseHas('stock_movements', [
      'store_id'    => $store->id,
      'origin_type' => StockMovement::OPTION_ORIGIN_TYPE_COUNT,
      'user_id'     => $user->id,
      'description' => 'Generado por Conteo.'
    ]);

  }
}