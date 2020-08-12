<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      app()['cache']->forget('spatie.permission.cache');
      
      Role::create(['id' => 0, 'name' => config('app.role_super_admin_name'), 'level' => 0])->givePermissionTo(Permission::all());
      
      $json = File::get("database/data/roles.json");
      $roles = json_decode($json);
      
      foreach ($roles as $role) {
        Role::create([
          'name'  => $role->name, 
          'level' => $role->level
        ]);
      }
    }
}
