<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreControllerTest extends ApiTestCase
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
  public function a_guest_cannot_access_to_store_resources()
  {
    $this->getJson(route('stores.index'))->assertUnauthorized();
    $this->getJson(route('stores.show', rand()))->assertUnauthorized();
    $this->postJson(route('stores.store'))->assertUnauthorized();
    $this->putJson(route('stores.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('stores.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_store_resources()
  {
    $this->signIn();
    
    $randomStoreId = Store::all()->random()->id;

    $this->getJson(route('stores.index'))->assertForbidden();
    $this->getJson(route('stores.show', $randomStoreId))->assertForbidden();
    $this->postJson(route('stores.store'))->assertForbidden();
    $this->putJson(route('stores.update', $randomStoreId))->assertForbidden();
    $this->deleteJson(route('stores.destroy', $randomStoreId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_stores()
  {
    $user = $this->signInWithPermissionsTo(['stores.index']);

    $response = $this->getJson(route('stores.index'))
      ->assertOk();
    
    foreach (Store::visible($user)->limit(10)->get() as $store) {
      $response->assertJsonFragment($store->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_store()
  {
    $user = $this->signInWithPermissionsTo(['stores.show']);

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $this->getJson(route('stores.show', $store->id))
      ->assertOk()
      ->assertJson($store->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_store()
  {
    $this->signInWithPermissionsTo(['stores.store']);

    $attributes = factory(Store::class)->raw();

    $this->postJson(route('stores.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('stores', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_store()
  {
    $user = $this->signInWithPermissionsTo(['stores.update']);

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $attributes = factory(Store::class)->raw([
      'company_id'        => $store->company_id,
      'petty_cash_amount' => $store->petty_cash_amount,
    ]);

    $this->putJson(route('stores.update', $store->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('stores', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_store()
  {
    $user = $this->signInWithPermissionsTo(['stores.destroy']);

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $store->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $this->deleteJson(route('stores.destroy', $store->id))
      ->assertOk();

    $this->assertDatabaseMissing('stores', $store->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_stores_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('stores.options'))
      ->assertOk();

    $stores = Store::select(['id', 'name'])
      ->withOut('storeType', 'storeChain', 'storeFlag', 'locationType', 'storeFormat', 'socioeconomicLevel', 'state', 'municipality', 'zone', 'company', 'turns')
      ->limit(10)
      ->visible($user)
      ->get();

    foreach ($stores as $store) {
      $response->assertJsonFragment($store->toArray());
    }
  }
}