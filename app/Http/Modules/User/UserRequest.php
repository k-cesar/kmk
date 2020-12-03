<?php

namespace App\Http\Modules\User;

use Illuminate\Validation\Rule;
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
      'name'            => 'required|string|max:100',
      'role_id'         => 'required|integer|exists:roles,id|min:'.auth()->user()->role_id,
      'email'           => 'sometimes|nullable|string|email|max:255|unique:users',
      'company_id'      => 'required|integer|exists:companies,id',
      'username'        => 'required|string|max:100|alpha_dash|unique:users',
      'password'        => 'required|string|min:8|max:25|confirmed',
      'update_password' => 'sometimes|nullable|boolean',
      'stores'          => 'sometimes|array',
      'stores.*'        => 'exists:stores,id',
      'phone'           => [
        'required', 
        'digits_between:1,50',
        Rule::unique('users', 'phone')
          ->where(function ($query) {
            return $query->where('company_id', $this->get('company_id'));
          }),
      ],

    ];

    if ($this->isMethod('PUT')) {
      $rules['username'] = "required|string|max:50|alpha_dash|unique:users,username,{$this->user->id}";
      $rules['email']    = "sometimes|nullable|string|email|max:255|unique:users,email,{$this->user->id}";
      $rules['password'] = 'sometimes|required|string|min:8|max:25|confirmed';

      $rules['phone'] = [
        'required', 
        'digits_between:1,50',
        Rule::unique('users', 'phone')
          ->where(function ($query) {
            return $query->where('company_id', $this->get('company_id'))
            ->where('id', '!=', $this->user->id);
          }),
        ];
    }

    return $rules;
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

    if (auth()->user()->role->level > 1) {
      $validatedData['company_id'] = auth()->user()->company_id;
    }

    return $validatedData;
  }
}
