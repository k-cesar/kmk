<?php

namespace App\Http\Modules\Location;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
      'name'              => 'required|string|max:100',
      'type'              => 'required|string|max:20',
      'active'            => 'required|string|in:'.implode(',', Location::getActiveOptions()),
      'company_id'        => 'required|exists:companies,id',
      'municipalities_id' => 'required|integer',
    ];

    return $rules;
  }
  
}
