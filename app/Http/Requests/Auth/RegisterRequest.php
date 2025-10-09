<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:50|unique:users',
            'nameEnterprise' => 'required|string|min:3|max:30',
            'sellerCode' => 'nullable|string|min:8|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome não pode ter menos de 3 caracteres',
            'name.max' => 'O nome não pode ter mais de 30 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.string' => 'A senha deve ser uma string',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'email.unique' => 'Este e-mail já está registrado',
            'nameEnterprise.required' => 'O nome da empresa é obrigatório',
            'nameEnterprise.string' => 'O nome da empresa deve ser uma string',
            'nameEnterprise.min' => 'O nome da empresa não pode ter menos de 3 caracteres',
            'nameEnterprise.max' => 'O nome da empresa não pode ter mais de 30 caracteres',
            'sellerCode.string' => 'O código do vendedor deve ser uma string',
            'sellerCode.min' => 'O código do vendedor deve ter pelo menos 8 caracteres',
            'sellerCode.max' => 'O código do vendedor não pode ter mais de 20 caracteres',
        ];
    }
}
