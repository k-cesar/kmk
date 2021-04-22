<?php

namespace App\Http\Modules\Maker;

use App\Rules\IUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class MakerRequest extends FormRequest
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
      'company_id' => 'sometimes|integer|exists:companies,id',
      'name'       => ['required', 'string', 'max:150',
        (new IUniqueRule('makers'))
          ->whereIn('company_id', [0, $this->get('company_id')])
          ->ignore($this->maker),
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

    $validatedData['company_id'] = auth()->user()->company_id;

    return $validatedData;
  }
  
}
