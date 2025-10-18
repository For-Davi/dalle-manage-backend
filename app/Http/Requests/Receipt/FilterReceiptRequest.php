<?php

namespace App\Http\Requests\Receipt;

use Illuminate\Foundation\Http\FormRequest;

class FilterReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active' => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'active.in' => 'O status ativo deve ser 0 (inativo) ou 1 (ativo).',
        ];
    }
}
