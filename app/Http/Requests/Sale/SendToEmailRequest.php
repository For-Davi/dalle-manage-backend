<?php

namespace App\Http\Requests\Sale;

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
            'saleID' => 'required|exists:sales,id',
            'email' => 'required|email|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
            'email.required' => 'O campo do email é obrigatório',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.max' => 'O e-mail não pode exceder 100 caracteres.',
        ];
    }
}
