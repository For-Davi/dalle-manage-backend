<?php

namespace App\Http\Requests\DalleAdm\Seller;

use Illuminate\Foundation\Http\FormRequest;

class LoginSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'nullable|string',
            'cpf' => 'required_without:token|nullable|string|max:11|min:11',
            'password' => 'required_without:token|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required_without' => 'Informe seu CPF para realizar o login.',
            'cpf.min' => 'O CPF deve conter exatamente 11 dígitos.',
            'cpf.max' => 'O CPF deve conter exatamente 11 dígitos.',
            'cpf.string' => 'O formato do CPF informado é inválido.',
            'password.required_without' => 'A senha é obrigatória para acessar sua conta.',
            'password.string' => 'A senha informada deve ser uma string válida.',
            'token.string' => 'O token de autenticação informado é inválido.',
        ];
    }
}
