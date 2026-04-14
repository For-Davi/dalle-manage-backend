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

                    if ($role && $role->is_system === 1) {
                        $fail('O perfil Master não pode ser excluído.');
                    }
                },
            ],
            'newRoleID' => 'required|integer|exists:roles,id',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'roleID' => $this->route('roleID'),
            'newRoleID' => $this->route('newRoleID'),
        ]);
    }

    public function messages(): array
    {
        return [
            'roleID.required' => 'O identificador do perfil é obrigatório.',
            'roleID.integer' => 'O identificador do perfil deve ser um número inteiro.',
            'roleID.exists' => 'O perfil selecionado para exclusão não foi encontrado.',

            'newRoleID.required' => 'É necessário informar um novo perfil para transferir os registros.',
            'newRoleID.integer' => 'O identificador do novo perfil deve ser um número inteiro.',
            'newRoleID.exists' => 'O perfil de destino informado não existe.',
        ];
    }
}
