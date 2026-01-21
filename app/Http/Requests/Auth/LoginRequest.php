<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'nullable|string',
            'email' => 'required_without:token|nullable|string|email|max:50',
            'password' => 'required_without:token|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without' => 'Por favor, informe seu e-mail para realizar o login.',
            'email.string' => 'O e-mail informado não é válido.',
            'email.email' => 'Digite um endereço de e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 50 caracteres.',
            'password.required_without' => 'A senha é obrigatória para acessar sua conta.',
            'password.string' => 'A senha informada é inválida.',
        ];
    }
}
