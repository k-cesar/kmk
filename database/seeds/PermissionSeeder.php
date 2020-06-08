<?php

use Illuminate\Database\Seeder;
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

        $json = File::get("database/data/permissions.json");
        $permissions = json_decode($json);
        foreach ($permissions as $permission) {
            foreach ($permission->labels as $label) {
                Permission::create([
                    'name'       => $label->name,
                    'route_name' => $label->route_name,
                    'group'      => $permission->group,
                    'level'      => $permission->level,
                ]);
            }
        }
    }
}
