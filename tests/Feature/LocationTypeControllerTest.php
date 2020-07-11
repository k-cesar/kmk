<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\LocationType\LocationType;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LocationTypeControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'LocationTypeSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_location_type_resources()
  {
    $this->getJson(route('location-types.index'))->assertUnauthorized();
    $this->getJson(route('location-types.show', rand()))->assertUnauthorized();
    $this->postJson(route('location-types.store'))->assertUnauthorized();
    $this->putJson(route('location-types.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('location-types.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_location_type_resources()
  {
    $this->signIn();
    
    $randomLocationTypeId = LocationType::all()->random()->id;

    $this->getJson(route('location-types.index'))->assertForbidden();
    $this->getJson(route('location-types.show', $randomLocationTypeId))->assertForbidden();
    $this->postJson(route('location-types.store'))->assertForbidden();
    $this->putJson(route('location-types.update', $randomLocationTypeId))->assertForbidden();
    $this->deleteJson(route('location-types.destroy', $randomLocationTypeId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_location_types()
  {

    $role = $this->getRoleWithPermissionsTo(['location-types.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('location-types.index'))
      ->assertOk();
    
    foreach (LocationType::limit(10)->get() as $locationType) {
      $response->assertJsonFragment($locationType->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_location_type()
  {
    $role = $this->getRoleWithPermissionsTo(['location-types.show']);
    $user = $this->signInWithRole($role);

    $locationType = factory(LocationType::class)->create();

    $this->getJson(route('location-types.show', $locationType->id))
      ->assertOk()
      ->assertJson($locationType->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_location_type()
  {
    $role = $this->getRoleWithPermissionsTo(['location-types.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(LocationType::class)->raw();

    $this->postJson(route('location-types.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('location_types', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_location_type()
  {
    $this->withExceptionHandling();

    $role = $this->getRoleWithPermissionsTo(['location-types.update']);
    $user = $this->signInWithRole($role);

    $locationType = factory(LocationType::class)->create();

    $attributes = factory(LocationType::class)->raw();

    $this->putJson(route('location-types.update', $locationType->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('location_types', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_location_type()
  {
    $role = $this->getRoleWithPermissionsTo(['location-types.destroy']);
    $user = $this->signInWithRole($role);

    $locationType = factory(LocationType::class)->create();

    $this->deleteJson(route('location-types.destroy', $locationType->id))
      ->assertOk();

    $this->assertDatabaseMissing('location_types', $locationType->toArray());
  }

}