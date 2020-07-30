<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Turn\Turn;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TurnControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'TurnSeeder']);
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
  public function an_user_with_role_with_permission_can_see_all_turns()
  {

    $role = $this->getRoleWithPermissionsTo(['turns.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('turns.index'))
      ->assertOk();
    
    foreach (Turn::limit(10)->get() as $turn) {
      $response->assertJsonFragment($turn->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_turn()
  {
    $role = $this->getRoleWithPermissionsTo(['turns.show']);
    $user = $this->signInWithRole($role);

    $turn = factory(Turn::class)->create();

    $this->getJson(route('turns.show', $turn->id))
      ->assertOk()
      ->assertJson($turn->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_turn()
  {
    $role = $this->getRoleWithPermissionsTo(['turns.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Turn::class)->raw();

    $this->postJson(route('turns.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('turns', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_turn()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['turns.update']);
    $user = $this->signInWithRole($role);

    $turn = factory(Turn::class)->create();

    $attributes = factory(Turn::class)->raw();

    $this->putJson(route('turns.update', $turn->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('turns', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_turn()
  {
    $role = $this->getRoleWithPermissionsTo(['turns.destroy']);
    $user = $this->signInWithRole($role);

    $turn = factory(Turn::class)->create();

    $this->deleteJson(route('turns.destroy', $turn->id))
      ->assertOk();

    $this->assertDatabaseMissing('turns', $turn->toArray());
  }

}