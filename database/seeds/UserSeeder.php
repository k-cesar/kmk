<?php

use App\Http\Modules\User\User;
use Illuminate\Database\Seeder;
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
      'name'      => env('USER_SUPER_ADMIN_NAME'),
      'email'     => env('USER_SUPER_ADMIN_EMAIL'),
      'username'  => env('USER_SUPER_ADMIN_EMAIL'),
      'password'  => Hash::make(env('USER_SUPER_ADMIN_PASSWORD')),
      'role_id'   => 0,
    ])->assignRole(config('app.role_super_admin_name'));
    
    factory(User::class)->create([
      'name'      => 'Stalin Sánchez',
      'username'  => 'stalin',
      'email'     => 'stalin@kemok.io',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Nelson Torres',
      'username'  => 'nelson',
      'email'     => 'ntorres144@gmail.com',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Diego Leal',
      'username'  => 'diego',
      'email'     => 'diego@kemok.io',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Cesar Salazar',
      'username'  => 'cesar',
      'email'     => 'cesar@kemok.io',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Luis de León',
      'username'  => 'luis_deleon',
      'email'     => 'luis.deleon@userlab.co',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));
    
    factory(User::class)->create([
      'name'      => 'Nelson Rodriguez',
      'username'  => 'estuardo',
      'email'     => 'estuardo@userlab.co',
      'role_id'   => 1,
      ])->assignRole(config('app.role_super_admin_name'));
      
    factory(User::class)->create([
      'name'      => 'Luis Moreno',
      'username'  => 'luis_moreno',
      'email'     => 'luis.moreno@userlab.co',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Carlos Moreno',
      'username'  => 'carlos_moreno',
      'email'     => 'carlos.moreno@userlab.co',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Jhonatan López',
      'username'  => 'jhonatan_lopez',
      'email'     => 'jhonatan.lopez@kemok.io',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class)->create([
      'name'      => 'Luis Villegas',
      'username'  => 'luis_villegas',
      'email'     => 'luis.villegas@kemok.io',
      'role_id'   => 1,
    ])->assignRole(config('app.role_super_admin_name'));

    factory(User::class, 2)->create();

  }
}
