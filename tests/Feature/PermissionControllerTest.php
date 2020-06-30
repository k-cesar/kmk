<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Support\Helper;
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

        $this->seed(['PermissionSeeder', 'RoleSeeder', 'UserSeeder']);
    }

    /**
     * @test
     */
    public function a_guest_cannot_access_to_permission_resources()
    {
        $this->getJson(route('permissions.index'))->assertUnauthorized();
        $this->getJson(route('permissions.show', rand()))->assertUnauthorized();
        $this->postJson(route('permissions.store'))->assertUnauthorized();
        $this->putJson(route('permissions.update', rand()))->assertUnauthorized();
        $this->deleteJson(route('permissions.destroy', rand()))->assertUnauthorized();
        $this->deleteJson(route('permissions.clean'))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_permission_resources()
    {
        $this->signIn();

        $randomPermissionId = Permission::all()->random()->id;

        $this->getJson(route('permissions.index'))->assertForbidden();
        $this->getJson(route('permissions.show', $randomPermissionId))->assertForbidden();
        $this->postJson(route('permissions.store'))->assertForbidden();
        $this->putJson(route('permissions.update', $randomPermissionId))->assertForbidden();
        $this->deleteJson(route('permissions.destroy', $randomPermissionId))->assertForbidden();
        $this->deleteJson(route('permissions.clean'))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_see_all_permissions()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.index']);
        $user = $this->signInWithRole($role);

        $response = $this->getJson(route('permissions.index'))
            ->assertSuccessful();
        
        foreach (Permission::limit(10)->get() as $permission) {
            $response->assertSee($permission->name)
                ->assertSee($permission->level);
        }
        
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_permissions()
    {
        $user = $this->signInWithPermissionsTo(['permissions.index']);

        $response = $this->getJson(route('permissions.index'))
            ->assertSuccessful();
        
        foreach (Permission::limit(10)->get() as $permission) {
            $response->assertSee($permission->name)
                ->assertSee($permission->level);
        }
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_see_a_permission()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.show']);
        $user = $this->signInWithRole($role);

        $permission = factory(Permission::class)->create();

        $this->getJson(route('permissions.show', $permission->id))
            ->assertSuccessful()
            ->assertSee($permission->name)
            ->assertSee($permission->route_name)
            ->assertSee($permission->level)
            ->assertSee($permission->group);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_a_permission()
    {
        $user = $this->signInWithPermissionsTo(['permissions.show']);

        $permission = factory(Permission::class)->create();

        $this->getJson(route('permissions.show', $permission->id))
            ->assertSuccessful()
            ->assertSee($permission->name)
            ->assertSee($permission->route_name)
            ->assertSee($permission->level)
            ->assertSee($permission->group);
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_store_a_permission()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.store']);
        $user = $this->signInWithRole($role);
        
        $attributes = factory(Permission::class)->raw();

        $this->postJson(route('permissions.store'), $attributes);

        $this->assertDatabaseHas('permissions', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_permission()
    {
        $user = $this->signInWithPermissionsTo(['permissions.store']);
        
        $permission = factory(Permission::class)->make();

        $this->postJson(route('permissions.store'), $permission->toArray());

        $this->assertDatabaseHas('permissions', $permission->toArray());
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_update_a_permission()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.update']);
        $user = $this->signInWithRole($role);

        $permission = factory(Permission::class)->create();

        $attributes = factory(Permission::class)->raw();

        $this->putJson(route('permissions.update', $permission->id), $attributes);

        $this->assertDatabaseHas('permissions', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_update_a_permission()
    {
        $user = $this->signInWithPermissionsTo(['permissions.update']);

        $permission = factory(Permission::class)->create();

        $attributes = factory(Permission::class)->raw();

        $this->putJson(route('permissions.update', $permission->id), $attributes);

        $this->assertDatabaseHas('permissions', $attributes);
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_destroy_a_permission()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.destroy']);
        $user = $this->signInWithRole($role);

        $permission = factory(Permission::class)->create();
        
        $this->deleteJson(route('permissions.destroy', $permission->id));

        $this->assertDatabaseMissing('permissions', $permission->toArray());
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_destroy_a_permission()
    {
        $user = $this->signInWithPermissionsTo(['permissions.destroy']);

        $permission = factory(Permission::class)->create();
        
        $this->deleteJson(route('permissions.destroy', $permission->id));

        $this->assertDatabaseMissing('permissions', $permission->toArray());
    }

    /**
     * @test
     */
    public function an_user_with_role_with_permission_can_clean_a_permissions()
    {
        $role = $this->getRoleWithPermissionsTo(['permissions.clean']);
        $user = $this->signInWithRole($role);
        
        factory(Permission::class, 10)->create();

        $this->assertNotEquals(0, Helper::getUnnecessaryPermissions()->count());
        $this->deleteJson(route('permissions.clean'));
        $this->assertEquals(0, Helper::getUnnecessaryPermissions()->count());

    }

    /**
     * @test
     */
    public function an_user_with_permission_can_clean_a_permissions()
    {        
        $user = $this->signInWithPermissionsTo(['permissions.clean']);

        factory(Permission::class, 10)->create();
        
        $this->assertNotEquals(0, Helper::getUnnecessaryPermissions()->count());
        $this->deleteJson(route('permissions.clean'));
        $this->assertEquals(0, Helper::getUnnecessaryPermissions()->count());

    }
}