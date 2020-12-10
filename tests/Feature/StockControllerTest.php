<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockControllerTest extends ApiTestCase
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
  public function a_guest_cannot_access_to_stock_resources()
  {
    $this->getJson(route('stocks.index'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_stock_resources()
  {
    $this->signIn();
    
    $this->getJson(route('stocks.index'))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_stocks()
  {
    $user = $this->signInWithPermissionsTo(['stocks.index']);

    $store = Store::has('products')->first();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $this->getJson(route('stocks.index', ['store_id' => $store->id]))
      ->assertSee('misuse of aggregate function MAX()')
      ->assertSee('stock_movements');
  }

  
}