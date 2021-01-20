<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\User\User;
use App\Http\Modules\Store\Store;
use App\Http\Modules\Company\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends ApiTestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $this->seed(['PermissionSeeder']);
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
    $user = $this->signInWithPermissionsTo(['users.index']);

    $response = $this->getJson(route('users.index'))
        ->assertOk();
    
    foreach (User::limit(10)->withOut('stores')->visible($user)->get() as $user) {
      $response->assertJsonFragment($user->toArray());
    }
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_see_a_user()
  {
    $user = $this->signInWithPermissionsTo(['users.show']);

    $newUser = factory(User::class)->create([
      'role_id'    => $user->role_id,
      'company_id' => $user->company_id,
    ]);

    $this->getJson(route('users.show', $newUser->id))
      ->assertOk()
      ->assertJson($newUser->toArray());
  }

  /**
   * @test
   */
  public function an_user_with_permission_can_store_a_user()
  {
    factory(Company::class)->create(['id' => 0, 'deleted_at' => now()]);

    $user = $this->signInWithPermissionsTo(['users.store']);

    $user->company->update(['allow_add_users' => true]);

    $stores = factory(Store::class, 2)->create(['company_id' => $user->company_id]);

    if ($user->role->level > 2) {
      $user->stores()->sync($stores->pluck('id'));
    }

    $attributes = factory(User::class)->raw([
      'role_id'    => $user->role_id,
      'company_id' => $user->company_id,
    ]);

    $attributes['password'] = 'password';
    $attributes['password_confirmation'] = $attributes['password'];
    $attributes['stores'] = $stores->pluck('id');

    $this->postJson(route('users.store'), $attributes)
      ->assertCreated();
    
    unset($attributes['password']);
    unset($attributes['password_confirmation']);
    unset($attributes['remember_token']);
    unset($attributes['stores']);

    if ($user->role_id == 1) {
      $attributes['company_id'] = 0;
    }

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
    factory(Company::class)->create(['id' => 0, 'deleted_at' => now()]);

    $user = $this->signInWithPermissionsTo(['users.update']);

    $stores = factory(Store::class, 2)->create(['company_id' => $user->company_id]);

    if ($user->role->level > 2) {
      $user->stores()->sync($stores->pluck('id'));
    }

    $attributes = factory(User::class)->raw([
      'role_id'    => $user->role_id,
      'company_id' => $user->company_id,
    ]);

    $attributes['stores'] = $stores->pluck('id');

    unset($attributes['password']);

    $this->putJson(route('users.update', $user->id), $attributes)
      ->assertOk();

    unset($attributes['update_password']);
    unset($attributes['remember_token']);
    unset($attributes['stores']);

    if ($user->role_id == 1) {
      $attributes['company_id'] = 0;
    }

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
    $user = $this->signInWithPermissionsTo(['users.destroy']);

    $newUser = factory(User::class)->create([
      'role_id'    => $user->role_id,
      'company_id' => $user->company_id,
    ]);

    $this->deleteJson(route('users.destroy', $newUser->id))
      ->assertOk();

    $this->assertDatabaseMissing('users', $newUser->toArray());
  }

}