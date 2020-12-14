<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Support\Arr;
use App\Http\Modules\Deposit\Deposit;
use App\Http\Modules\Deposit\DepositImage;
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
    $user = $this->signInWithPermissionsTo(['deposits.index']);

    $response = $this->getJson(route('deposits.index'))
      ->assertOk();
    
    foreach (Deposit::visibleThroughStore($user)->limit(10)->get() as $deposit) {
      $response->assertJsonFragment($deposit->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_deposit()
  {
    $user = $this->signInWithPermissionsTo(['deposits.show']);

    $deposit = factory(Deposit::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $deposit->store()->first()->company_id]);
      } else {
        $user->stores()->sync($deposit->store_id);
      }
    }

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

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $user->stores()->sync($storeTurn->store_id);
      }
    }

    $attributes = factory(Deposit::class)->raw([
      'store_id'      => $storeTurn->store_id,
      'store_turn_id' => $storeTurn->id,
      'created_by'    => $user->id,
    ]);

    $images = factory(DepositImage::class, 2)->raw();

    $this->postJson(route('deposits.store'), array_merge($attributes, ['images' => $images]))
      ->assertCreated();
    
    $this->assertDatabaseHas('deposits', Arr::except($attributes, ['date']));

    foreach ($images as $image) {
      $this->assertDatabaseHas('deposit_images', Arr::except($image, 'deposit_id'));
    }

  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_deposit()
  {
    $user = $this->signInWithPermissionsTo(['deposits.update']);

    $storeTurn = factory(StoreTurn::class)->create(['is_open' => true]);

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $user->update(['company_id' => $storeTurn->store->company_id]);
      } else {
        $user->stores()->sync($storeTurn->store_id);
      }
    }

    $attributes = factory(Deposit::class)->raw([
      'store_id'      => $storeTurn->store_id,
      'store_turn_id' => $storeTurn->id,
      'created_by'    => $user->id,
    ]);

    $deposit = factory(Deposit::class)->create(['store_id' => $storeTurn->store_id]);

    $images = factory(DepositImage::class, 2)->raw(['deposit_id' => $deposit->id]);
    
    $this->putJson(route('deposits.update', $deposit->id), array_merge($attributes, ['images' => $images]))
      ->assertOk();

    $this->assertDatabaseHas('deposits', Arr::only($attributes, ['deposit_number', 'amount']));

    foreach ($images as $image) {
      $this->assertDatabaseHas('deposit_images', $image);
    }
  }
}