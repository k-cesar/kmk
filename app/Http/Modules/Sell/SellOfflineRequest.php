<?php

namespace App\Http\Modules\Sell;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class SellOfflineRequest extends FormRequest
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
      'store_id'                   => 'required|integer|store_visible',
      'sells'                      => 'required|array',
      'sells.*.payment_method_id'  => 'required|exists:payment_methods,id',
      'sells.*.client_id'          => 'sometimes|exists:clients,id',
      'sells.*.name'               => 'required|string|max:250',
      'sells.*.nit'                => 'required|digits_between:1,15',
      'sells.*.address'            => 'sometimes|nullable|string|max:50',
      'sells.*.phone'              => 'required_with:client_id|string|max:50',
      'sells.*.email'              => 'required_with:client_id|string|email',
      'sells.*.description'        => 'sometimes|nullable|string|max:250',
      'sells.*.items'              => 'required|array',
      'sells.*.items.*.quantity'   => 'required|numeric|min:0',
      'sells.*.items.*.unit_price' => 'required|numeric|min:0',
      'sells.*.items.*.type'       => 'required|string|in:PRESENTATION,COMBO',
    ];

    foreach ($this->get('sells', []) as $indexSell => $sell) {
      $rules["sells.$indexSell.store_turn_id"] = [
        'required',
        Rule::exists('store_turns', 'id')
          ->where(function ($query) use ($sell) {
            return $query->where('id', $sell['store_turn_id'])
              ->where('store_id', $this->get('store_id'));
        }),
      ];

      foreach($sell['items'] ?? [] as $indexItem => $item) {

        if ($item['type'] ?? false) {
          $tableName = $item['type'] == 'PRESENTATION' ? 'presentations' : 'presentation_combos';
        } else {
          break;
        }

        $rules["sells.$indexSell.items.$indexItem.id"] = [
          'required',
          'integer',
          function ($attribute, $value, $fail) use ($item, $tableName) {
            $exists = DB::table($tableName)
              ->where('id', $value)
              ->whereNull('deleted_at')
              ->first();

            if (!$exists) {
              $fail("El producto ({$item['type']}) [$value] seleccionado no existe");
            }
          },
        ];

        if ($item['type'] == 'COMBO') {
          array_push($rules["sells.$indexSell.items.$indexItem.id"],
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
    }

    return $rules;
  }
  
}
