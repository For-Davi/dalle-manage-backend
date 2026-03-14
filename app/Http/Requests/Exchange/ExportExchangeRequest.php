<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class ExportExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exchangeID' => 'required|exists:exchanges,id',
        ];
    }

    public function messages(): array
    {
        return [
            'exchangeID.required' => 'O ID da troca é obrigatório',
            'exchangeID.exists' => 'O ID da troca informado não existe',
        ];
    }
}
