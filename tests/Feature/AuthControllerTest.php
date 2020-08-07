<?php   

namespace Tests\Feature;

use Tests\ApiTestCase;
use Illuminate\Support\Arr;
use App\Http\Modules\User\User;
use App\Support\Helper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthControllerTest extends ApiTestCase
{
  use DatabaseMigrations, RefreshDatabase;

  /**
   * @test
   */
  public function a_guest_can_register()
  {
    $attributes = factory(User::class)->raw();
    $attributes['password'] = 'password';
    $attributes['password_confirmation'] = $attributes['password']; 

    $this->postJson(route('api.register'), $attributes)
      ->assertCreated()
      ->assertJson(Arr::only($attributes, 'username'));
  }

  /**
   * @test
   */
  public function a_user_can_login()
  {
    $user = factory(User::class)->create();

    $attributes = [
      'username' => Helper::encrypt($user->username, env('PASSPHRASE')),
      'password' => Helper::encrypt('password', env('PASSPHRASE')),
    ];

    $this->post(route('api.login'), $attributes)
      ->assertOk()
      ->assertJsonStructure([
        'token' => ['access_token', 'token_type', 'expires_in'],
        'permissions'
      ]);
  }

  /**
   * @test
   */
  public function a_user_can_reset_their_password()
  {
    $user = $this->signIn();

    $attributes = [
      'actual_password'       => 'password',
      'password'              => '12345678',
      'password_confirmation' => '12345678',
    ];

    $this->put(route('api.reset'), $attributes)
      ->assertOk()
      ->assertSee('Cambio de Password Exitoso');

    $this->post(route('api.login'), ['username' => Helper::encrypt($user->username, env('PASSPHRASE')), 'password' => Helper::encrypt('password', env('PASSPHRASE'))])
      ->assertUnauthorized();
    
    $this->postJson(route('api.logout'))
      ->assertOk();

    $this->post(route('api.login'), ['username' => Helper::encrypt($user->username, env('PASSPHRASE')), 'password' => Helper::encrypt('12345678', env('PASSPHRASE'))])
      ->assertOk()
      ->assertJsonStructure([
        'token' => ['access_token', 'token_type', 'expires_in'],
        'permissions'
      ]);
  }

  /**
   * @test
   */
  public function a_user_can_logout()
  {
    $this->signIn();

    $this->postJson(route('api.logout'))
      ->assertOk();
    
    $this->postJson(route('api.logout'))
      ->assertUnauthorized();
  }


  /**
   * @test
   */
  public function a_user_can_refresh_their_token()
  {
    $this->signIn();

    $response = $this->getJson(route('api.refresh'))
      ->assertOk()
      ->assertJsonStructure(['access_token', 'token_type', 'expires_in'])
      ->decodeResponseJson();
    
    $this->getJson(route('api.refresh'))
      ->assertJson(['message' => "The token has been blacklisted"]);

    $this->withHeaders(['Authorization' => "Bearer ".$response['access_token']]);

    $this->postJson(route('api.logout'))
      ->assertOk();
  }

  /**
   * @test
   */
  public function a_user_can_get_the_authenticated_user()
  {
    $user = factory(User::class)->create();

    $this->signIn($user);

    $this->getJson('/api/auth/me')
      ->assertOK()
      ->assertJson($user->toArray());
  }

}