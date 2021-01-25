<?php

namespace App\Http\Modules\User;

use Illuminate\Validation\Rule;
use App\Http\Modules\Store\Store;
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
      'email'           => 'sometimes|nullable|string|email|max:255|unique:users'.($this->user ? ",{$this->user->id}" : ''),
      'company_id'      => 'exclude_if:role_id,1|required|integer|exists:companies,id,deleted_at,NULL',
      'username'        => 'required|string|max:100|alpha_dash|unique:users'.($this->user ? ",{$this->user->id}" : ''),
      'password'        => 'required|string|min:8|max:25|confirmed',
      'update_password' => 'sometimes|nullable|boolean',
      'stores'          => 'sometimes|array',
      'stores.*'        => ['integer',
        function ($attribute, $value, $fail) {
          $store = Store::where('id', $value)
            ->where('company_id', $this->get('company_id'))
            ->visible(auth()->user())
            ->first();

          if (!$store) {
            $fail("El campo {$attribute} es inválido.");
          }
        },
      ],
      'phone'           => ['required', 'digits_between:1,50',
        Rule::unique('users')
          ->where('company_id', $this->get('company_id'))
          ->ignore($this->user),
      ],
    ];

    if ($this->isMethod('PUT')) {
      $rules['password'] = 'sometimes|required|string|min:8|max:25|confirmed';
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

    if ($validatedData['role_id'] == 1) {
      $validatedData['company_id'] = 0;
    } else if (auth()->user()->role->level > 1) {
      $validatedData['company_id'] = auth()->user()->company_id;
    }

    return $validatedData;
  }
}
