<?php

namespace App\Http\Modules\Brand;

use App\Rules\IUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
      'maker_id'   => 'required|integer|exists:makers,id,deleted_at,NULL',
      'company_id' => 'sometimes|integer|exists:companies,id',
      'name'       => ['required', 'string', 'max:150',
        (new IUniqueRule('brands'))
          ->where('maker_id', $this->get('maker_id'))
          ->where('company_id', $this->get('company_id', auth()->user()->company_id))
          ->ignore($this->brand),
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
