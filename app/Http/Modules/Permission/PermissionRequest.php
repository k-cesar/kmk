<?php

namespace App\Http\Modules\Permission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
            'name'       => 'required|string|min:3|max:30|unique:permissions',
            'route_name' => 'required|string|unique:permissions',
            'level'      => "required|integer|min:{$this->user()->getMinimunRoleLevel()}",
            'group'      => 'required|string|min:3|max:20',
        ];

        if ($this->isMethod('PUT')) {
            $rules = array_merge($rules, [
                'name'       => "required|string|min:3|max:30|unique:permissions,name,\"{$this->permission->name}\",name",
                'route_name' => "required|string|unique:permissions,route_name,{$this->permission->route_name},route_name",
            ]);
        }

        return $rules;
    }
}
