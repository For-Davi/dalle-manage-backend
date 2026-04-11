<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:30',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => [
                'required',
                'integer',
                'exists:permissions,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.min' => 'O nome do perfil deve ter pelo menos 1 caractere.',
            'name.max' => 'O nome do perfil não pode ter mais de 30 caracteres.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode exceder 500 caracteres.',
            'permissions.required' => 'É necessário informar as permissões para este perfil.',
            'permissions.array' => 'O formato das permissões é inválido.',
            'permissions.*.required' => 'O ID da permissão é obrigatório.',
            'permissions.*.integer' => 'O ID da permissão deve ser um número inteiro.',
            'permissions.*.exists' => 'Uma ou mais permissões informadas não existem no sistema.',
        ];
    }
}
