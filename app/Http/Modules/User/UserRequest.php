<?php

namespace App\Http\Modules\User;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'name'      => 'required|string|max:100',
      'last_name' => 'required|string|max:100',
      'username'  => 'required|string|max:50|alpha_dash|unique:users',
      'email'     => 'required|string|email|max:255|unique:users',
      'active'    => 'required|string|in:'.implode(',', User::getActiveOptions()),
      'user_type' => 'required|string|max:20',
      'password'  => 'required|string|min:8|confirmed',
      'roles.*'   => "sometimes|in:{$this->getAllowedRoles()}",
    ];

    if ($this->isMethod('PUT')) {
      $rules['username'] = "required|string|max:50|alpha_dash|unique:users,username,{$this->user->username},username";
      $rules['email']    = "required|string|email|max:255|unique:users,email,{$this->user->email},email";
      $rules['password'] = 'exclude_unless:update_password,true|required|string|min:8|confirmed';
    }

    return $rules;
  }

  /**
   * Get the allowed roles name for the authenticaed user
   *
   * @return string
   */
  protected function getAllowedRoles()
  {
    return Role::where('level', '>=', auth()->user()->getMinimunRoleLevel())
      ->get()
      ->pluck('name')
      ->implode(',');
  }

  /**
   * Get the validated data from the request.
   *
   * @return array
   */
  public function validated()
  {
    $validatedData = parent::validated();

    if (isset($validatedData['password'])) {
      $validatedData['password'] = Hash::make($validatedData['password']);
    }

    return $validatedData;
  }
}
