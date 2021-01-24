<?php

namespace App\Http\Modules\Deposit;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
      'store_id'        => 'required|integer|store_visible',
      'store_turn_id'   => "required|integer|exists:store_turns,id,store_id,{$this->get('store_id')},is_open,1,deleted_at,NULL",
      'deposit_number'  => 'required|string|max:100|unique:deposits,deposit_number'.($this->deposit ? ",{$this->deposit->id}" : ''),
      'amount'          => 'required|numeric|min:0',
      'images'          => 'required|array',
      'images.*.title'  => 'required|string|max:255|distinct',
      'images.*.base64' => 'required|string',
    ];

    return $rules;
  }
}
