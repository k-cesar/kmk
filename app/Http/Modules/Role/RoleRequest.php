<?php

namespace App\Http\Modules\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'name'  => 'required|unique:roles|alpha_dash|min:3|max:20',
            'level' => "required|integer|min:{$this->user()->getMinimunRoleLevel()}",
        ];

        if ($this->isMethod('PUT')) {
            $rules = array_merge($rules, [
                'name'  => "required|alpha_dash|min:3|max:20|unique:roles,name,{$this->role->name},name",
            ]);
        }

        return $rules;
    }
}
