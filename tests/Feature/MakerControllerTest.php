<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Maker\Maker;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MakerControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'MakerSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_maker_resources()
  {
    $this->getJson(route('makers.index'))->assertUnauthorized();
    $this->getJson(route('makers.show', rand()))->assertUnauthorized();
    $this->postJson(route('makers.store'))->assertUnauthorized();
    $this->putJson(route('makers.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('makers.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_maker_resources()
  {
    $this->signIn();
    
    $randomMakerId = Maker::all()->random()->id;

    $this->getJson(route('makers.index'))->assertForbidden();
    $this->getJson(route('makers.show', $randomMakerId))->assertForbidden();
    $this->postJson(route('makers.store'))->assertForbidden();
    $this->putJson(route('makers.update', $randomMakerId))->assertForbidden();
    $this->deleteJson(route('makers.destroy', $randomMakerId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_makers()
  {

    $role = $this->getRoleWithPermissionsTo(['makers.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('makers.index'))
      ->assertOk();
    
    foreach (Maker::limit(10)->get() as $maker) {
      $response->assertJsonFragment($maker->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_maker()
  {
    $role = $this->getRoleWithPermissionsTo(['makers.show']);
    $user = $this->signInWithRole($role);

    $maker = factory(Maker::class)->create();

    $this->getJson(route('makers.show', $maker->id))
      ->assertOk()
      ->assertJson($maker->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_maker()
  {
    $role = $this->getRoleWithPermissionsTo(['makers.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Maker::class)->raw();

    $this->postJson(route('makers.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('makers', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_maker()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['makers.update']);
    $user = $this->signInWithRole($role);

    $maker = factory(Maker::class)->create();

    $attributes = factory(Maker::class)->raw();

    $this->putJson(route('makers.update', $maker->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('makers', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_maker()
  {
    $role = $this->getRoleWithPermissionsTo(['makers.destroy']);
    $user = $this->signInWithRole($role);

    $maker = factory(Maker::class)->create();

    $this->deleteJson(route('makers.destroy', $maker->id))
      ->assertOk();

    $this->assertDatabaseMissing('makers', $maker->toArray());
  }

}