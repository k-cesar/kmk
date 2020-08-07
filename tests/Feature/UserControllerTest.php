<?php

namespace Tests\Feature;

use App\Http\Modules\Store\Store;
use Tests\ApiTestCase;
use App\Http\Modules\User\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder']);
  }

  /**
   * @test
   */
  public function a_guest_cannot_access_to_user_resources()
  {
    $this->getJson(route('users.index'))->assertUnauthorized();
    $this->getJson(route('users.show', rand()))->assertUnauthorized();
    $this->postJson(route('users.store'))->assertUnauthorized();
    $this->putJson(route('users.update', rand()))->assertUnauthorized();
    $this->deleteJson(route('users.destroy', rand()))->assertUnauthorized();
  }

  /**
   * @test
   */
  public function an_user_without_permission_cannot_access_to_user_resources()
  {
    $this->signIn();

    $randomUserId = User::all()->random()->id;

    $this->getJson(route('users.index'))->assertForbidden();
    $this->getJson(route('users.show', $randomUserId))->assertForbidden();
    $this->postJson(route('users.store'))->assertForbidden();
    $this->putJson(route('users.update', $randomUserId))->assertForbidden();
    $this->deleteJson(route('users.destroy', $randomUserId))->assertForbidden();
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_all_users()
  {
    $this->signInWithPermissionsTo(['users.index']);

    $response = $this->getJson(route('users.index'))
        ->assertOk();
    
    foreach (User::limit(10)->withOut('stores')->get() as $user) {
      $response->assertJsonFragment($user->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_user()
  {
    $this->signInWithPermissionsTo(['users.show']);

    $newUser = factory(User::class)->create();

    $this->getJson(route('users.show', $newUser->id))
      ->assertOk()
      ->assertJson($newUser->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_user()
  {
    $this->signInWithPermissionsTo(['users.store']);

    $stores = factory(Store::class, 2)->create();

    $attributes = factory(User::class)->raw();
    $attributes['password'] = 'password';
    $attributes['password_confirmation'] = $attributes['password'];
    $attributes['stores'] = $stores->pluck('id');

    $this->postJson(route('users.store'), $attributes)
      ->assertCreated();
    
    unset($attributes['password']);
    unset($attributes['password_confirmation']);
    unset($attributes['remember_token']);
    unset($attributes['stores']);

    $this->assertDatabaseHas('users', $attributes);

    foreach ($stores as $store) {
      $this->assertDatabaseHas('store_users', ['store_id' => $store->id, 'user_id' => User::all()->last()->id]);
    }
  }


  /**
   * @test
   */
  public function an_user_with_permission_can_update_a_user()
  {
    $user = $this->signInWithPermissionsTo(['users.update']);

    $stores = factory(Store::class, 2)->create();

    $attributes = factory(User::class)->raw();
    $attributes['update_password'] = false;
    $attributes['stores'] = $stores->pluck('id');

    $this->putJson(route('users.update', $user->id), $attributes)
      ->assertOk();

    unset($attributes['password']);
    unset($attributes['update_password']);
    unset($attributes['remember_token']);
    unset($attributes['stores']);

    $this->assertDatabaseHas('users', $attributes);
    foreach ($stores as $store) {
      $this->assertDatabaseHas('store_users', ['store_id' => $store->id, 'user_id' => $user->id]);
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_destroy_a_user()
  {
    $this->signInWithPermissionsTo(['users.destroy']);

    $newUser = factory(User::class)->create();

    $this->deleteJson(route('users.destroy', $newUser->id))
      ->assertOk();

    $this->assertDatabaseMissing('users', $newUser->toArray());
  }

}