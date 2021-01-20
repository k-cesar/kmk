<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Http\Modules\User\User;
use Spatie\Permission\Models\Role;
use App\Http\Modules\Company\Company;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    $role = Role::where('id', '!=', 0)->inRandomOrder()->first() ?? factory(Role::class)->create();

    if ($role->level == 1) {
        $company = Company::where('id', 0)->withTrashed()->first() ?? factory(Company::class)->create(['id' => 0, 'deleted_at' => now()]);
    } else {
        $company = Company::inRandomOrder()->first() ?? factory(Company::class);
    }

    return [
        'name'              => $faker->name,
        'username'          => Str::slug($faker->unique()->userName),
        'email'             => $faker->unique()->safeEmail,
        'phone'             => rand(1000000, 9999999),
        'company_id'        => $company,
        'role_id'           => $role->id,
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',   // password
        'remember_token'    => Str::random(10),
    ];
});
