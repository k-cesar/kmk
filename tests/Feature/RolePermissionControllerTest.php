<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Spatie\Permission\Models\Permission;

class RolePermissionControllerTest extends ApiTestCase
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
    public function a_guest_cannot_access_to_role_permission_resources()
    {
        $randomRoleId = Role::all()->random()->id;

        $this->getJson(route('roles.permissions.index', $randomRoleId))->assertUnauthorized();
        $this->postJson(route('roles.permissions.store', $randomRoleId))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_role_permission_resources()
    {
        $this->signIn();

        $randomRoleId = Role::all()->random()->id;

        $this->getJson(route('roles.permissions.index', $randomRoleId))->assertForbidden();
        $this->postJson(route('roles.permissions.store', $randomRoleId))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_see_all_role_permissions()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.permissions.index']);
        $user = $this->signInWithRole($role);

        $response = $this->getJson(route('roles.permissions.index', $role->id))
            ->assertSuccessful();
        
        foreach ($role->getAllPermissions() as $permission) {
            $response->assertSee($permission->name)
                ->assertSee($permission->level);
        }
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_role_permission()
    {
        $user = $this->signInWithPermissionsTo(['roles.permissions.index']);

        $role = factory(Role::class)->create();
        $role->givePermissionTo(factory(Permission::class)->create());

        $response = $this->getJson(route('roles.permissions.index', $role->id))
            ->assertSuccessful();
        
        foreach ($role->getAllPermissions() as $permission) {
            $response->assertSee($permission->name)
                ->assertSee($permission->level);
        }
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_store_a_role_permission()
    {
        $role = $this->getRoleWithPermissionsTo(['roles.permissions.store']);
        $user = $this->signInWithRole($role);


        $role = factory(Role::class)->create();
        $permission1 = factory(Permission::class)->create();
        $permission2 = factory(Permission::class)->create();

        $attributes = [
            'permissions' => [$permission1->id, $permission2->id],
        ];

        $this->postJson(route('roles.permissions.store', $role->id), $attributes);

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission1->id
        ]);

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission2->id
        ]);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_role_permission()
    {
        $user = $this->signInWithPermissionsTo(['roles.permissions.store']);

        $role = factory(Role::class)->create();

        $permission1 = factory(Permission::class)->create();
        $permission2 = factory(Permission::class)->create();

        $attributes = [
            'permissions' => [$permission1->id, $permission2->id],
        ];

        $this->postJson(route('roles.permissions.store', $role->id), $attributes);

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission1->id
        ]);

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission2->id
        ]);
    }
}