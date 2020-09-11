<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Support\Arr;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\StoreTurn\StoreTurn;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepositControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'DepositSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_deposit_resources()
  {
    $this->getJson(route('deposits.index'))->assertUnauthorized();
    $this->getJson(route('deposits.show', rand()))->assertUnauthorized();
    $this->postJson(route('deposits.store'))->assertUnauthorized();
    $this->putJson(route('deposits.update', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_deposit_resources()
  {
    $this->signIn();
    
    $randomDepositId = Deposit::all()->random()->id;

    $this->getJson(route('deposits.index'))->assertForbidden();
    $this->getJson(route('deposits.show', $randomDepositId))->assertForbidden();
    $this->postJson(route('deposits.store'))->assertForbidden();
    $this->putJson(route('deposits.update', $randomDepositId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_deposits()
  {
    $this->signInWithPermissionsTo(['deposits.index']);

    $response = $this->getJson(route('deposits.index'))
      ->assertOk();
    
    foreach (Deposit::limit(10)->get() as $deposit) {
      $response->assertJsonFragment($deposit->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_deposit()
  {
    $this->signInWithPermissionsTo(['deposits.show']);

    $deposit = factory(Deposit::class)->create();

    $this->getJson(route('deposits.show', $deposit->id))
      ->assertOk()
      ->assertJson($deposit->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_deposit()
  {
    $user = $this->signInWithPermissionsTo(['deposits.store']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    $attributes = factory(Deposit::class)->raw([
      'store_id'      => $storeTurn->store_id,
      'store_turn_id' => $storeTurn->id,
      'created_by'    => $user->id,
    ]);

    $imagesUrls = ['http://test.com', 'https://example.com'];

    $this->postJson(route('deposits.store'), array_merge($attributes, ['images_urls' => $imagesUrls]))
      ->assertCreated();
    
    $this->assertDatabaseHas('deposits', Arr::except($attributes, ['date']));

    foreach ($imagesUrls as $imageUrl) {
      $this->assertDatabaseHas('deposit_images', ['url' => $imageUrl]);
    }

  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_deposit()
  {
    $user = $this->signInWithPermissionsTo(['deposits.update']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    $attributes = factory(Deposit::class)->raw([
      'store_id'      => $storeTurn->store_id,
      'store_turn_id' => $storeTurn->id,
      'created_by'    => $user->id,
    ]);

    $deposit = factory(Deposit::class)->create();

    $imagesUrls = ['http://test.com', 'https://example.com'];
    
    $this->putJson(route('deposits.update', $deposit->id), array_merge($attributes, ['images_urls' => $imagesUrls]))
      ->assertOk();

    $this->assertDatabaseHas('deposits', Arr::only($attributes, ['deposit_number', 'amount']));

    foreach ($imagesUrls as $imageUrl) {
      $this->assertDatabaseHas('deposit_images', [
        'deposit_id' => $deposit->id,
        'url'        => $imageUrl
      ]);
    }
  }
}