<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    app()['cache']->forget('spatie.permission.cache');

    $json = File::get("database/data/permissions_groups.json");
    $permissionsGroups = json_decode($json);
    foreach ($permissionsGroups as $permissionGroup) {
      foreach ($permissionGroup->permissions as $permission) {
        $permissionCreated = Permission::create([
          'name'  => $permission->name,
          'level' => $permission->level,
          'group' => $permissionGroup->name
        ]);

        foreach ($permission->routes as $route) {
          DB::table('permission_route')
            ->insert([
              'permission_id' => $permissionCreated->id,
              'route'         => $route,
              'created_at'    => now(),
              'updated_at'    => now(),
            ]);
        }
      }
    }
  }
}
