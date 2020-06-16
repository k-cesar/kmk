<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Location\Location;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LocationControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed();
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_location_resources()
  {
    $this->getJson(route('locations.index'))->assertUnauthorized();
    $this->getJson(route('locations.show', rand()))->assertUnauthorized();
    $this->postJson(route('locations.store'))->assertUnauthorized();
    $this->putJson(route('locations.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('locations.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_location_resources()
  {
    $this->signIn();

    $randomLocationId = Location::all()->random()->id;

    $this->getJson(route('locations.index'))->assertForbidden();
    $this->getJson(route('locations.show', $randomLocationId))->assertForbidden();
    $this->postJson(route('locations.store'))->assertForbidden();
    $this->putJson(route('locations.update', $randomLocationId))->assertForbidden();
    $this->deleteJson(route('locations.destroy', $randomLocationId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_all_locations()
  {

    $role = $this->getRoleWithPermissionsTo(['locations.index']);
    $user = $this->signInWithRole($role);

    $response = $this->getJson(route('locations.index'))
      ->assertOk();
    
    foreach (Location::limit(10)->get() as $location) {
      $response->assertJsonFragment($location->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_see_a_location()
  {
    $role = $this->getRoleWithPermissionsTo(['locations.show']);
    $user = $this->signInWithRole($role);

    $location = factory(Location::class)->create();

    $this->getJson(route('locations.show', $location->id))
      ->assertOk()
      ->assertJson($location->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_store_a_location()
  {
    $role = $this->getRoleWithPermissionsTo(['locations.store']);
    $user = $this->signInWithRole($role);

    $attributes = factory(Location::class)->raw();

    $this->postJson(route('locations.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('locations', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_update_a_location()
  {
    $role = $this->getRoleWithPermissionsTo(['locations.update']);
    $user = $this->signInWithRole($role);

    $location = factory(Location::class)->create();

    $attributes = factory(Location::class)->raw();

    $this->putJson(route('locations.update', $location->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('locations', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_role_with_permission_can_destroy_a_location()
  {
    $role = $this->getRoleWithPermissionsTo(['locations.destroy']);
    $user = $this->signInWithRole($role);

    $location = factory(Location::class)->create();

    $this->deleteJson(route('locations.destroy', $location->id))
      ->assertOk();

    $this->assertDatabaseMissing('locations', $location->toArray());
  }

}