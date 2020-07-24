<?php

namespace Tests\Feature;

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

        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_role_resources()
    {
        $this->getJson(route('roles.index'))->assertUnauthorized();
        $this->getJson(route('roles.show', rand()))->assertUnauthorized();
        $this->postJson(route('roles.store'))->assertUnauthorized();
        $this->putJson(route('roles.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('roles.destroy', rand()))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_role_resources()
    {
        $this->signIn();

        $randomRoleId = Role::all()->random()->id;

        $this->getJson(route('roles.index'))->assertForbidden();
        $this->getJson(route('roles.show', $randomRoleId))->assertForbidden();
        $this->postJson(route('roles.store'))->assertForbidden();
        $this->putJson(route('roles.update', $randomRoleId))->assertForbidden();
        $this->deleteJson(route('roles.destroy', $randomRoleId))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_see_all_roles()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.index']);
        $user = $this->signInWithRole($role);

        $response = $this->getJson(route('roles.index'))
            ->assertSuccessful();
        
        foreach (Role::where('level', '>=', $user->getMinimunRoleLevel())->limit(10)->get() as $role) {
            $response->assertSee($role->name)
                ->assertSee($role->level);
        }
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_roles()
    {
        $user = $this->signInWithPermissionsTo(['roles.index']);

        $response = $this->getJson(route('roles.index'))
            ->assertSuccessful();
        
        foreach (Role::where('level', '>=', $user->getMinimunRoleLevel())->limit(10)->get() as $role) {
            $response->assertSee($role->name)
                ->assertSee($role->level);
        }
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_see_a_role()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.show']);
        $user = $this->signInWithRole($role);

        $this->getJson(route('roles.show', $role->id))
            ->assertSuccessful()
            ->assertSee($role->name)
            ->assertSee($role->route_name)
            ->assertSee($role->level)
            ->assertSee($role->group);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_role()
    {
        $user = $this->signInWithPermissionsTo(['roles.show']);

        $role = factory(Role::class)->create();

        $this->getJson(route('roles.show', $role->id))
            ->assertSuccessful()
            ->assertSee($role->name)
            ->assertSee($role->level);
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_store_a_role()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.store']);
        $user = $this->signInWithRole($role);

        $attributes = factory(Role::class)->raw();

        $this->postJson(route('roles.store'), $attributes);

        $this->assertDatabaseHas('roles', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_role()
    {
        $user = $this->signInWithPermissionsTo(['roles.store']);

        $attributes = factory(Role::class)->raw();

        $this->postJson(route('roles.store'), $attributes);

        $this->assertDatabaseHas('roles', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_update_a_role()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.update']);
        $user = $this->signInWithRole($role);

        $attributes = factory(Role::class)->raw();

        $this->putJson(route('roles.update', $role->id), $attributes);

        $this->assertDatabaseHas('roles', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_update_a_role()
    {
        $user = $this->signInWithPermissionsTo(['roles.update']);

        $role = factory(Role::class)->create();

        $attributes = factory(Role::class)->raw();

        $this->putJson(route('roles.update', $role->id), $attributes);

        $this->assertDatabaseHas('roles', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_destroy_a_role()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.destroy']);
        $user = $this->signInWithRole($role);

        $newRole = factory(Role::class)->create();

        $this->deleteJson(route('roles.destroy', $newRole->id));

        $this->assertDatabaseMissing('roles', $newRole->toArray());
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_destroy_a_role()
    {
        $user = $this->signInWithPermissionsTo(['roles.destroy']);

        $role = factory(Role::class)->create();

        $this->deleteJson(route('roles.destroy', $role->id));

        $this->assertDatabaseMissing('roles', $role->toArray());
    }

    /**
     * @test
     */
    public function an_user_can_see_all_roles_options()
    {
        $user = $this->signInWithRole(factory(Role::class)->create(['level' => 0]));

        $response = $this->getJson(route('roles.options'))
            ->assertSuccessful();
        
        foreach (Role::where('level', '>=', $user->getMinimunRoleLevel())->limit(10)->get() as $role) {
            $response->assertSee($role->id)
                ->assertSee(e($role->name));
        }
    }

}