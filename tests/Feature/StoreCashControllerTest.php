<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreCashControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'StoreSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_stores_cash_resources()
  {
    $this->getJson(route('stores.index'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_stores_cash_resources()
  {
    $this->signIn();
    
    $this->getJson(route('stores.index'))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_stores_cash()
  {
    $user = $this->signInWithPermissionsTo(['stores-cash.index']);


    $response = $this->getJson(route('stores-cash.index'))
      ->assertOk();

    $stores = Store::select('id', 'name', 'petty_cash_amount')
      ->visible($user)
      ->withOut('storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'company', 'turns')->limit(10)->get();
    
    foreach ($stores as $store) {
      $response->assertJsonFragment($store->toArray());
    }
  }
  
}