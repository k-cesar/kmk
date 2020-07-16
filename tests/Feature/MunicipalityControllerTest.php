<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Municipality\Municipality;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MunicipalityControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'MunicipalitySeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_municipality_resources()
  {
    $this->getJson(route('municipalities.index'))->assertUnauthorized();
    $this->getJson(route('municipalities.show', rand()))->assertUnauthorized();
    $this->postJson(route('municipalities.store'))->assertUnauthorized();
    $this->putJson(route('municipalities.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('municipalities.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_municipality_resources()
  {
    $this->signIn();
    
    $randomMunicipalityId = Municipality::all()->random()->id;

    $this->getJson(route('municipalities.index'))->assertForbidden();
    $this->getJson(route('municipalities.show', $randomMunicipalityId))->assertForbidden();
    $this->postJson(route('municipalities.store'))->assertForbidden();
    $this->putJson(route('municipalities.update', $randomMunicipalityId))->assertForbidden();
    $this->deleteJson(route('municipalities.destroy', $randomMunicipalityId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_municipalities()
  {

    $role = $this->getRoleWithPermissionsTo(['municipalities.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('municipalities.index'))
      ->assertOk();
    
    foreach (Municipality::limit(10)->get() as $municipality) {
      $response->assertJsonFragment($municipality->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_municipality()
  {
    $role = $this->getRoleWithPermissionsTo(['municipalities.show']);
    $user = $this->signInWithRole($role);

    $municipality = factory(Municipality::class)->create();

    $this->getJson(route('municipalities.show', $municipality->id))
      ->assertOk()
      ->assertJson($municipality->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_municipality()
  {
    $role = $this->getRoleWithPermissionsTo(['municipalities.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Municipality::class)->raw();

    $this->postJson(route('municipalities.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('municipalities', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_municipality()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['municipalities.update']);
    $user = $this->signInWithRole($role);

    $municipality = factory(Municipality::class)->create();

    $attributes = factory(Municipality::class)->raw();

    $this->putJson(route('municipalities.update', $municipality->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('municipalities', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_municipality()
  {
    $role = $this->getRoleWithPermissionsTo(['municipalities.destroy']);
    $user = $this->signInWithRole($role);

    $municipality = factory(Municipality::class)->create();

    $this->deleteJson(route('municipalities.destroy', $municipality->id))
      ->assertOk();

    $this->assertDatabaseMissing('municipalities', $municipality->toArray());
  }

}