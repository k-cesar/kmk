<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PermissionControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_permission_resources()
  {
    $this->getJson(route('permissions.index'))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_permission_resources()
  {
    $this->signIn();

    $randomPermissionId = Permission::all()->random()->id;

    $this->getJson(route('permissions.index'))->assertForbidden();
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_permissions()
  {
    $this->signInWithPermissionsTo(['permissions.index']);

    $response = $this->getJson(route('permissions.index'))
      ->assertSuccessful();

    foreach (Permission::limit(10)->get() as $permission) {
      $response->assertSee(utf8_decode($permission->name))
        ->assertSee($permission->level);
    }
  }
} 