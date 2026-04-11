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
            'identifier' => 'required|string|min:1|max:30',
            'typesID' => 'nullable|exists:types_receipt,id',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'O nome do recebimento é obrigatório.',
            'identifier.string' => 'O nome deve ser uma string.',
            'identifier.min' => 'O nome do recebimento deve ter pelo menos 1 caractere.',
            'identifier.max' => 'O nome do recebimento não pode ter mais de 30 caracteres.',
            'typesID.exists' => 'O tipo de recebimento é inválido.',
            'description.string' => 'A descrição deve ser um texto',
            'description.max' => 'A descrição não pode exceder 500 caracteres.',
        ];
    }
}
