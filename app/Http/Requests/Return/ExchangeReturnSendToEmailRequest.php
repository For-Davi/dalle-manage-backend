<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeReturnSendToEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'returnID' => 'required|exists:returns,id',
            'email' => 'required|email|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'returnID.required' => 'O ID da troca é obrigatório',
            'returnID.exists' => 'O ID da troca informada não existe',
            'email.required' => 'O campo do email é obrigatório',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.max' => 'O e-mail não pode exceder 100 caracteres.',
        ];
    }
}
