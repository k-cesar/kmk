<?php

namespace App\Http\Modules\Sell;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
      'store_id'           => 'required|integer|store_visible',
      'store_turn_id'      => "required|integer|exists:store_turns,id,store_id,{$this->get('store_id')},is_open,1,deleted_at,NULL",
      'payment_method_id'  => 'required|integer|visible_through_company:payment_methods',
      'name'               => 'required|string|max:250',
      'nit'                => 'required|string|alpha_num|max:15',
      'address'            => 'required|string|max:50',
      'phone'              => 'present|nullable|string|max:50',
      'email'              => 'present|nullable|string|email|max:100',
      'description'        => 'sometimes|nullable|string|max:250',
      'items'              => 'required|array',
      'items.*.quantity'   => 'required|numeric|min:0',
      'items.*.unit_price' => 'required|numeric|min:0',
      'items.*.type'       => 'required|string|in:PRESENTATION,COMBO',
    ];

    foreach($this->get('items', []) as $index => $item) {

      if ($item['type'] ?? false) {
        $tableName = $item['type'] == 'PRESENTATION' ? 'presentations' : 'presentation_combos';
      } else {
        break;
      }

      $rules["items.$index.id"] = ['required', 'integer',
        function ($attribute, $value, $fail) use ($item, $tableName) {
          $exists = DB::table($tableName)
            ->where('id', $value)
            ->whereNull('deleted_at')
            ->when(auth()->user()->role->level > 1, function ($query) {
              $query->whereIn('company_id', [0, auth()->user()->company_id]);
            })
            ->first();

          if (!$exists) {
            $fail("El producto ({$item['type']}) [$value] seleccionado no existe");
          }
        },
      ];

      if ($item['type'] == 'COMBO') {
        array_push($rules["items.$index.id"],
          function ($attribute, $value, $fail) {
            $exists = DB::table('presentation_combos_detail')
              ->where('presentation_combo_id', $value)
              ->first();

            if (!$exists) {
              $fail("El combo [$value] seleccionado no posee ninguna presentación asociada");
            }
          }
        );
      }
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
    
    $validatedData['nit'] = strtoupper($validatedData['nit']);

    return $validatedData;
  }
  
}
