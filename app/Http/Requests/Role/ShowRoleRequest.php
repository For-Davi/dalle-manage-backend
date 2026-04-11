<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class ShowRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roleID' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'roleID.required' => 'O ID do papel é obrigatório',
            'roleID.exists' => 'O ID do papel informado não existe.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
