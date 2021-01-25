<?php

namespace App\Http\Modules\StockCount;

use Illuminate\Foundation\Http\FormRequest;

class StockCountRequest extends FormRequest
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
            'store_id'   => 'required|integer|store_visible',
            'count_date' => 'required|date|date_format:Y-m-d',
            'status'     => 'required|in:'.implode(',', StockCount::getOptionsStatus()),
        ];

        if ($this->isMethod('PUT')) {
            $rules['status'] = 'required|in:'.implode(',', StockCount::getOptionStatusForUpdate());
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

      if ($this->isMethod('POST')) {
        $validatedData['created_by'] = auth()->user()->id;
      }

      return $validatedData;
    }
}
