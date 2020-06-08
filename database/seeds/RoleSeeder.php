<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
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
      
      Role::create(['name' => config('app.role_super_admin_name'), 'level' => 0])->givePermissionTo(Permission::all());
    }
}
