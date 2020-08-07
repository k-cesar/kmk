<?php

namespace Tests\Feature;

use App\Http\Modules\User\User;
use Tests\ApiTestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RoleControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['RoleSeeder']);
  }

  /**
   * @test
   */
  public function an_user_can_see_all_roles_options()
  {
    $user = factory(User::class)->create(['role_id' => 1]);

    $this->signIn($user);

    $response = $this->getJson(route('roles.options'))
      ->assertOk();
    
    $roles = Role::select(['id', 'name'])
      ->where('level', '>=', auth()->user()->role->level)
      ->limit(10)
      ->get();

    foreach ($roles as $role) {
      $response->assertJsonFragment($role->toArray());
    }
  }
}