<?php

namespace App\Http\Requests\Role;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:roles,id',
            'name' => 'required|string|min:1|max:30',
            'description' => 'nullable|string|max:500',
            'permissions' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $slugs = array_keys($value);
                    $allSlugs = Permission::pluck('slug')->toArray();

                    $invalidSlugs = array_diff($slugs, $allSlugs);
                    if (! empty($invalidSlugs)) {
                        $fail('Uma ou mais permissões informadas são inválidas ou não existem no sistema.');
                    }

                    $missingSlugs = array_diff($allSlugs, $slugs);
                    if (! empty($missingSlugs)) {
                        $fail('Todas as permissões do sistema devem ser informadas.');
                    }
                },
            ],
            'permissions.*' => 'required|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do perfil é obrigatório.',
            'id.integer' => 'O identificador do perfil deve ser um número inteiro.',
            'id.exists' => 'O perfil informado não foi encontrado.',
            'name.required' => 'O nome do perfil é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.min' => 'O nome do perfil deve ter pelo menos 1 caractere.',
            'name.max' => 'O nome do perfil não pode ter mais de 30 caracteres.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode exceder 500 caracteres.',
            'permissions.required' => 'É necessário informar as permissões para este perfil.',
            'permissions.array' => 'O formato das permissões é inválido.',
            'permissions.*.required' => 'O status da permissão é obrigatório.',
            'permissions.*.integer' => 'O valor da permissão deve ser um número inteiro.',
            'permissions.*.in' => 'O valor da permissão deve ser 0 (desativado) ou 1 (ativado).',
        ];
    }
}
