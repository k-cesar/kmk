<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\Zone\Zone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ZoneControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder', 'ZoneSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_zone_resources()
  {
    $this->getJson(route('zones.index'))->assertUnauthorized();
    $this->getJson(route('zones.show', rand()))->assertUnauthorized();
    $this->postJson(route('zones.store'))->assertUnauthorized();
    $this->putJson(route('zones.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('zones.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_zone_resources()
  {
    $this->signIn();
    
    $randomZoneId = Zone::all()->random()->id;

    $this->getJson(route('zones.index'))->assertForbidden();
    $this->getJson(route('zones.show', $randomZoneId))->assertForbidden();
    $this->postJson(route('zones.store'))->assertForbidden();
    $this->putJson(route('zones.update', $randomZoneId))->assertForbidden();
    $this->deleteJson(route('zones.destroy', $randomZoneId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_zones()
  {
    $this->signInWithPermissionsTo(['zones.index']);

    $response = $this->getJson(route('zones.index'))
      ->assertOk();
    
    foreach (Zone::limit(10)->get() as $zone) {
      $response->assertJsonFragment($zone->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_zone()
  {
    $this->signInWithPermissionsTo(['zones.show']);

    $zone = factory(Zone::class)->create();

    $this->getJson(route('zones.show', $zone->id))
      ->assertOk()
      ->assertJson($zone->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_zone()
  {
    $this->signInWithPermissionsTo(['zones.store']);

    $attributes = factory(Zone::class)->raw();

    $this->postJson(route('zones.store'), $attributes)
      ->assertCreated();
    
    $this->assertDatabaseHas('zones', $attributes);
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_zone()
  {
    $this->signInWithPermissionsTo(['zones.update']);

    $zone = factory(Zone::class)->create();

    $attributes = factory(Zone::class)->raw();

    $this->putJson(route('zones.update', $zone->id), $attributes)
      ->assertOk();

    $this->assertDatabaseHas('zones', $attributes);
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_zone()
  {
    $this->signInWithPermissionsTo(['zones.destroy']);

    $zone = factory(Zone::class)->create();

    $this->deleteJson(route('zones.destroy', $zone->id))
      ->assertOk();

    $this->assertDatabaseMissing('zones', $zone->toArray());
  }

}