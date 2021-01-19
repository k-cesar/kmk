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

    $permissionsToCreate = [];
    $permissionRoutesToCreate = [];

    $nextId = DB::getDefaultConnection() == 'pgsql' ? DB::select("select nextval('permissions_id_seq')")[0]->nextval : 1;

    $json = File::get("database/data/permissions_groups.json");
    $permissionsGroups = json_decode($json);
    foreach ($permissionsGroups as $permissionGroup) {
      foreach ($permissionGroup->permissions as $permission) {
        $permissionsToCreate[] = [
          'id'         => $nextId,
          'name'       => $permission->name,
          'level'      => $permission->level,
          'group'      => $permissionGroup->name,
          'guard_name' => 'api',
          'created_at' => now(),
          'updated_at' => now(),
        ];

        foreach ($permission->routes as $route) {
          $permissionRoutesToCreate[] = [
            'permission_id' => $nextId,
            'route'         => $route,
            'created_at'    => now(),
            'updated_at'    => now(),
          ];
        }

        $nextId++;

      }
    }

    Permission::insert($permissionsToCreate);
    
    DB::table('permission_routes')
      ->insert($permissionRoutesToCreate);

    if (DB::getDefaultConnection() == 'pgsql') {
      DB::select("select setval('permissions_id_seq', $nextId)");
    }
    
  }

}
