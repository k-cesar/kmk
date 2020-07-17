<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\State\State;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StateControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'StateSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_state_resources()
  {
    $this->getJson(route('states.index'))->assertUnauthorized();
    $this->getJson(route('states.show', rand()))->assertUnauthorized();
    $this->postJson(route('states.store'))->assertUnauthorized();
    $this->putJson(route('states.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('states.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_state_resources()
  {
    $this->signIn();
    
    $randomStateId = State::all()->random()->id;

    $this->getJson(route('states.index'))->assertForbidden();
    $this->getJson(route('states.show', $randomStateId))->assertForbidden();
    $this->postJson(route('states.store'))->assertForbidden();
    $this->putJson(route('states.update', $randomStateId))->assertForbidden();
    $this->deleteJson(route('states.destroy', $randomStateId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_states()
  {

    $role = $this->getRoleWithPermissionsTo(['states.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('states.index'))
      ->assertOk();
    
    foreach (State::limit(10)->get() as $state) {
      $response->assertJsonFragment($state->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_state()
  {
    $role = $this->getRoleWithPermissionsTo(['states.show']);
    $user = $this->signInWithRole($role);

    $state = factory(State::class)->create();

    $this->getJson(route('states.show', $state->id))
      ->assertOk()
      ->assertJson($state->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_state()
  {
    $role = $this->getRoleWithPermissionsTo(['states.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(State::class)->raw();

    $this->postJson(route('states.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('states', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_state()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['states.update']);
    $user = $this->signInWithRole($role);

    $state = factory(State::class)->create();

    $attributes = factory(State::class)->raw();

    $this->putJson(route('states.update', $state->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('states', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_state()
  {
    $role = $this->getRoleWithPermissionsTo(['states.destroy']);
    $user = $this->signInWithRole($role);

    $state = factory(State::class)->create();

    $this->deleteJson(route('states.destroy', $state->id))
      ->assertOk();

    $this->assertDatabaseMissing('states', $state->toArray());
  }

}