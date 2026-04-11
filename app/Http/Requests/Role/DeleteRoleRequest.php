<?php

namespace App\Http\Requests\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roleID' => [
                'required',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);

                    if ($role && strtoupper($role->name) === 'MASTER') {
                        $fail('O perfil Master não pode ser excluído.');
                    }
                },
            ],
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'roleID' => $this->route('roleID'),
        ]);
    }

    public function messages(): array
    {
        return [
            'roleID.required' => 'O identificador do perfil é obrigatório.',
            'roleID.integer' => 'O identificador do perfil deve ser um número inteiro.',
            'roleID.exists' => 'O perfil informado não foi encontrado.',
        ];
    }
}
