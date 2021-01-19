<?php

namespace Tests\Feature;

use Tests\ApiTestCase;
use App\Http\Modules\User\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPermissionControllerTest extends ApiTestCase
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
    public function a_guest_cannot_access_to_user_permission_resources()
    {
        $randomUserId = factory(User::class)->create()->id;

        $this->getJson(route('users.permissions.index', $randomUserId))->assertUnauthorized();
        $this->postJson(route('users.permissions.store', $randomUserId))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function an_user_without_permission_cannot_access_to_user_permission_resources()
    {
        $this->signIn();

        $randomUserId = User::all()->random()->id;

        $this->getJson(route('users.permissions.index', $randomUserId))->assertForbidden();
        $this->postJson(route('users.permissions.store', $randomUserId))->assertForbidden();
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_see_all_user_permissions()
    {
        $user = $this->signInWithPermissionsTo(['users.permissions.index']);

        $user = factory(User::class)->create([
            'role_id'    => $user->role_id,
            'company_id' => $user->company_id,
        ]);

        $user->givePermissionTo(factory(Permission::class)->create());

        $response = $this->getJson(route('users.permissions.index', $user->id))
            ->assertSuccessful();
        
        foreach ($user->getAllPermissions() as $permission) {
            $response->assertSee(explode(' ', $permission->name)[0])
                ->assertSee($permission->id)
                ->assertSee($permission->level)
                ->assertSee($permission->group);
        }
    }

    /**
     * @test
     */
    public function an_user_with_permission_can_store_a_user_permission()
    {
        $user = $this->signInWithPermissionsTo(['users.permissions.store']);

        $user = factory(User::class)->create([
            'role_id'    => $user->role_id,
            'company_id' => $user->company_id,
        ]);

        $permission1 = factory(Permission::class)->create();
        $permission2 = factory(Permission::class)->create();

        $attributes = [
            'permissions' => [$permission1->id, $permission2->id],
        ];

        $this->postJson(route('users.permissions.store', $user->id), $attributes)
            ->assertOk();

        $this->assertDatabaseHas('model_has_permissions', [
            'permission_id' => $permission1->id,
            'model_type'    => User::class,
            'model_id'      => $user->id,
        ]);

        $this->assertDatabaseHas('model_has_permissions', [
            'permission_id' => $permission2->id,
            'model_type'    => User::class,
            'model_id'      => $user->id,
        ]);
    }
}