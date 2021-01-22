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
      'name'         => 'Señor Tendero',
      'symbol'       => '_',
      'abbreviation' => '_',
      'description'  => '_',
      'disabled'     => 1,
      'deleted_at'   => now(),
    ]);

    Country::create([
      'id'          => 0,
      'name'        => 'Señor Tendero',
      'currency_id' => 0,
      'deleted_at'  => now(),
    ]);

    Company::create([
      'id'                 => '0',
      'name'               => 'Señor Tendero',
      'reason'             => '_',
      'regime'             => '_',
      'nit'                => '_',
      'phone'              => '_',
      'address'            => '_',
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
      'name'       => 'Stalin Sánchez',
      'username'   => 'stalin',
      'email'      => 'stalin@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Nelson Torres',
      'username'   => 'nelson',
      'email'      => 'ntorres144@gmail.com',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Diego Leal',
      'username'   => 'diego',
      'email'      => 'diego@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Cesar Salazar',
      'username'   => 'cesar',
      'email'      => 'cesar@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Luis de León',
      'username'   => 'luis_deleon',
      'email'      => 'luis.deleon@userlab.co',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());
    
    factory(User::class)->create([
      'name'       => 'José González',
      'username'   => 'jose',
      'email'      => 'jose@userlab.co',
      'company_id' => 0,
      'role_id'    => 1,
      ])->syncPermissions(Permission::all());
      
    factory(User::class)->create([
      'name'       => 'Luis Moreno',
      'username'   => 'luis_moreno',
      'email'      => 'luis.moreno@userlab.co',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Carlos Moreno',
      'username'   => 'carlos_moreno',
      'email'      => 'carlos.moreno@userlab.co',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Jhonatan López',
      'username'   => 'jhonatan_lopez',
      'email'      => 'jhonatan.lopez@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Luis Villegas',
      'username'   => 'luis_villegas',
      'email'      => 'luis.villegas@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());

    factory(User::class)->create([
      'name'       => 'Jimy Sagastume',
      'username'   => 'jimy',
      'email'      => 'jimy@kemok.io',
      'company_id' => 0,
      'role_id'    => 1,
    ])->syncPermissions(Permission::all());
  }
}