<?php

namespace App\Http\Requests\SellerRegistration;

use Illuminate\Foundation\Http\FormRequest;

class CreateSellerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do associado é obrigatório.',
            'name.string' => 'O nome do associado deve ser um texto.',
            'name.min' => 'O nome do associado deve ter pelo menos 1 caractere.',
            'name.max' => 'O nome do associado não pode exceder 100 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.max' => 'O e-mail não pode exceder 100 caracteres.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode exceder 20 caracteres.',
            'description.string' => 'A descrição deve ser um texto.',
            'description.max' => 'A descrição não pode exceder 500 caracteres.',
        ];
    }
}
