<?php

namespace App\Http\Requests\Commission;

use Illuminate\Foundation\Http\FormRequest;

class ExportCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => 'required|date_format:m/Y',
            'sellerID' => 'required|exists:employees,id',
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'O período é obrigatório.',
            'period.date_format' => 'O período deve estar no formato válido: mm/yyyy.',
            'sellerID.required' => 'O vendedor é obrigatório.',
            'sellerID.exists' => 'O vendedor informado não foi encontrada.',
        ];
    }
}
