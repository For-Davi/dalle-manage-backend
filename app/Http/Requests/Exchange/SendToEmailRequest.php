<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class SendToEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exchangeID' => 'required|exists:exchanges,id',
            'email' => 'required|email|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'exchangeID.required' => 'O ID da troca é obrigatório',
            'exchangeID.exists' => 'O ID da troca informada não existe',
            'email.required' => 'O campo do email é obrigatório',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.max' => 'O e-mail não pode exceder 100 caracteres.',
        ];
    }
}
