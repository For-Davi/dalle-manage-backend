<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

class ExportExchangeReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'returnID' => 'required|exists:returns,id',
        ];
    }

    public function messages(): array
    {
        return [
            'returnID.required' => 'O ID da troca é obrigatório',
            'returnID.exists' => 'O ID da troca informado não existe',
        ];
    }
}
