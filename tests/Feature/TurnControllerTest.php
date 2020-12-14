<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use App\Http\Modules\Store\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TurnControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'TurnSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_turn_resources()
  {
    $this->getJson(route('turns.index'))->assertUnauthorized();
    $this->getJson(route('turns.show', rand()))->assertUnauthorized();
    $this->postJson(route('turns.store'))->assertUnauthorized();
    $this->putJson(route('turns.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('turns.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_turn_resources()
  {
    $this->signIn();
    
    $randomTurnId = Turn::all()->random()->id;

    $this->getJson(route('turns.index'))->assertForbidden();
    $this->getJson(route('turns.show', $randomTurnId))->assertForbidden();
    $this->postJson(route('turns.store'))->assertForbidden();
    $this->putJson(route('turns.update', $randomTurnId))->assertForbidden();
    $this->deleteJson(route('turns.destroy', $randomTurnId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_turns()
  {
    $user = $this->signInWithPermissionsTo(['turns.index']);

    $response = $this->getJson(route('turns.index'))
      ->assertOk();
    
    foreach (Turn::visibleThroughStore($user)->limit(10)->get() as $turn) {
      $response->assertJsonFragment($turn->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_turn()
  {
    $user = $this->signInWithPermissionsTo(['turns.show']);

    $turn = factory(Turn::class)->create();

    if ($user->role->level > 1) {
        if ($user->role->level == 2) {
          $user->update(['company_id' => $turn->store()->first()->company_id]);
        } else {
          $user->stores()->sync([$turn->store_id]);
        }
    }

    $this->getJson(route('turns.show', $turn->id))
      ->assertOk()
      ->assertJson($turn->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_turn()
  {
    $user = $this->signInWithPermissionsTo(['turns.store']);

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $store->update(['company_id' => $user->company_id]);
      } else {
        $user->stores()->sync($store->id);
      }
    }

    $attributes = factory(Turn::class)->raw(['store_id' => $store->id]);

    $this->postJson(route('turns.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('turns', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_turn()
  {
    $user = $this->signInWithPermissionsTo(['turns.update']);

    $turn = factory(Turn::class)->create();

    $store = factory(Store::class)->create();

    if ($user->role->level > 1) {
      if ($user->role->level == 2) {
        $turn->store->update(['company_id' => $user->company_id]);
        $store->update(['company_id' => $user->company_id]);
      } else {
        $user->stores()->sync([$store->id, $turn->store_id]);
      }
    }

    $attributes = factory(Turn::class)->raw(['store_id' => $store->id]);

    $this->putJson(route('turns.update', $turn->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('turns', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_turn()
  {
    $user = $this->signInWithPermissionsTo(['turns.destroy']);
    
    $user->role->update(['level' => 2]);

    $turn = factory(Turn::class)->create();

    if ($user->role->level > 1) {
        if ($user->role->level == 2) {
          $user->update(['company_id' => $turn->store()->first()->company_id]);
        } else {
          $user->stores()->sync([$turn->store_id]);
        }
    }

    $this->deleteJson(route('turns.destroy', $turn->id))
      ->assertOk();

    $this->assertDatabaseMissing('turns', $turn->toArray());
  }
}