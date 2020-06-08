<?php

use App\Http\Modules\User\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    factory(User::class)->create([
      'name'   => env('USER_SUPER_ADMIN_NAME'),
      'email'    => env('USER_SUPER_ADMIN_EMAIL'),
      'password' => Hash::make(env('USER_SUPER_ADMIN_PASSWORD')),
    ])->assignRole(config('app.role_super_admin_name'));
    

    factory(User::class)->create([
      'name' => 'Stalin Sánchez',
      'email'  => 'stalin@kemok.io',
    ])->assignRole(config('app.role_super_admin_name'));
  }
}
