<?php

use Illuminate\Support\Str;
use App\Http\Modules\User\User;
use Illuminate\Database\Seeder;
use App\Http\Modules\Client\Client;
use Illuminate\Support\Facades\Hash;
use App\Http\Modules\Company\Company;
use App\Http\Modules\Country\Country;
use App\Http\Modules\Currency\Currency;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Currency::create([
      'id'           => 0,
      'name'         => 'Moneda 0',
      'symbol'       => '00',
      'abbreviation' => 'Abrebiación 0',
      'description'  => 'Descripción 0',
      'disabled'     => 1,
      'deleted_at'   => now(),
    ]);

    Country::create([
      'id'          => 0,
      'name'        => 'País 0',
      'currency_id' => 0,
      'deleted_at'  => now(),
    ]);

    Company::create([
      'id'                 => '0',
      'name'               => 'Empresa 0',
      'reason'             => 'Razón Social 0',
      'regime'             => 'Régimen 0',
      'nit'                => '00000000',
      'phone'              => '00000000',
      'address'            => 'Dirección 0',
      'country_id'         => 0,
      'currency_id'        => 0,
      'allow_fel'          => 1,
      'allow_add_users'    => 1,
      'allow_add_stores'   => 1,
      'allow_add_products' => 1,
      'deleted_at'         => now(),
    ]);

    Client::create([
      'id'           => 0,
      'name'         => 'Consumidor Final',
      'type'         => Client::OPTION_TYPE_INDIVIDUAL,
      'country_id'   => 0,
      'nit'          => 'CF',
      'address'      => 'Ciudad',
      'sex'          => Client::OPTION_SEX_MALE,
      'biometric_id' => Str::random(50),
      'birthdate'    => now(),
      'deleted_at'   => now(),
    ]);

    factory(User::class)->create([
      'name'       => env('USER_SUPER_ADMIN_NAME'),
      'email'      => env('USER_SUPER_ADMIN_EMAIL'),
      'username'   => env('USER_SUPER_ADMIN_EMAIL'),
      'password'   => Hash::make(env('USER_SUPER_ADMIN_PASSWORD')),
      'company_id' => 0,
      'role_id'    => 0,
    ])->assignRole(config('app.role_super_admin_name'))
      ->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Stalin Sánchez',
      'username'  => 'stalin',
      'email'     => 'stalin@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Nelson Torres',
      'username'  => 'nelson',
      'email'     => 'ntorres144@gmail.com',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Diego Leal',
      'username'  => 'diego',
      'email'     => 'diego@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Cesar Salazar',
      'username'  => 'cesar',
      'email'     => 'cesar@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Luis de León',
      'username'  => 'luis_deleon',
      'email'     => 'luis.deleon@userlab.co',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());
    
    factory(User::class)->create([
      'name'      => 'Nelson Rodriguez',
      'username'  => 'estuardo',
      'email'     => 'estuardo@userlab.co',
      'role_id'   => 1,
      ])->syncPermissions(Permission::all());
      
    factory(User::class)->create([
      'name'      => 'Luis Moreno',
      'username'  => 'luis_moreno',
      'email'     => 'luis.moreno@userlab.co',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Carlos Moreno',
      'username'  => 'carlos_moreno',
      'email'     => 'carlos.moreno@userlab.co',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Jhonatan López',
      'username'  => 'jhonatan_lopez',
      'email'     => 'jhonatan.lopez@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Luis Villegas',
      'username'  => 'luis_villegas',
      'email'     => 'luis.villegas@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'      => 'Jimy Sagastume',
      'username'  => 'jimy',
      'email'     => 'jimy@kemok.io',
      'role_id'   => 1,
    ])->syncPermissions(Permission::all());
  }
}