<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Currency\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'CurrencySeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_currency_resources()
  {
    $this->getJson(route('currencies.index'))->assertUnauthorized();
    $this->getJson(route('currencies.show', rand()))->assertUnauthorized();
    $this->postJson(route('currencies.store'))->assertUnauthorized();
    $this->putJson(route('currencies.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('currencies.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_currency_resources()
  {
    $this->signIn();

    $randomCurrencyId = Currency::all()->random()->id;

    $this->getJson(route('currencies.index'))->assertForbidden();
    $this->getJson(route('currencies.show', $randomCurrencyId))->assertForbidden();
    $this->postJson(route('currencies.store'))->assertForbidden();
    $this->putJson(route('currencies.update', $randomCurrencyId))->assertForbidden();
    $this->deleteJson(route('currencies.destroy', $randomCurrencyId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_currencies()
  {
    $this->signInWithPermissionsTo(['currencies.index']);

    $response = $this->getJson(route('currencies.index'))
      ->assertOk();
    
    foreach (Currency::limit(10)->get() as $currency) {
      $response->assertJsonFragment($currency->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_currency()
  {
    $this->signInWithPermissionsTo(['currencies.show']);

    $currency = factory(Currency::class)->create();

    $this->getJson(route('currencies.show', $currency->id))
      ->assertOk()
      ->assertJson($currency->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_currency()
  {
    $this->signInWithPermissionsTo(['currencies.store']);

    $attributes = factory(Currency::class)->raw();

    $this->postJson(route('currencies.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('currencies', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_currency()
  {
    $this->signInWithPermissionsTo(['currencies.update']);

    $currency = factory(Currency::class)->create();

    $attributes = factory(Currency::class)->raw();

    $this->putJson(route('currencies.update', $currency->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('currencies', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_currency()
  {
    $this->signInWithPermissionsTo(['currencies.destroy']);

    $currency = factory(Currency::class)->create();

    $this->deleteJson(route('currencies.destroy', $currency->id))
      ->assertOk();

    $this->assertDatabaseMissing('currencies', $currency->toArray());
  }

  /**
   * @test
   */
  public function an_user_can_see_all_currencies_options()
  {
    $user = $this->signIn();

    $response = $this->getJson(route('currencies.options'))
      ->assertOk();
    
    $currencies = Currency::select(['id', 'name'])
      ->limit(10)
      ->get();

    foreach ($currencies as $currency) {
      $response->assertJsonFragment($currency->toArray());
    }
  }

}