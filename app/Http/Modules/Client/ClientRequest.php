<?php

namespace App\Http\Modules\Client;

use App\Support\Helper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation()
  {
    $this->merge([
      'nit' => Helper::strToUpper($this->nit)
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rules = [
      'name'         => 'required|string|max:250',
      'type'         => 'required|in:'.implode(',', Client::getOptionsTypes()),
      'country_id'   => 'required|integer|exists:countries,id,deleted_at,NULL',
      'address'      => 'sometimes|nullable|string|max:500',
      'sex'          => 'required|in:'.implode(',', Client::getOptionsSex()),
      'biometric_id' => 'sometimes|nullable|string|max:1000',
      'birthdate'    => 'required|date|date_format:Y-m-d|before:today|after:1900-01-01',
      'phone'        => 'present|nullable|max:50',
      'email'        => 'present|nullable|email|max:100',
      'nit'          => ['required', 'string', 'max:15', 'regex:/^\d+k?$/i',
        Rule::unique('clients')
          ->where('country_id', $this->get('country_id'))
          ->ignore($this->client),
      ],
    ];

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

    if (auth()->user()->role->level > 1) {
      $validatedData['country_id'] = auth()->user()->company->country_id;
    }

    return $validatedData;
  }

}
