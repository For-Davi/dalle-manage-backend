<?php

namespace App\Http\Requests\Commission;

use Illuminate\Foundation\Http\FormRequest;

class FilterCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => 'nullable|date_format:d-m-Y',
            'endDate' => 'nullable|date_format:d-m-Y',
            'sellerID' => 'nullable|exists:employees,id',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.date_format' => 'A data inicial deve estar no formato dia-mês-ano (ex: 27-03-2026).',
            'endDate.date_format' => 'A data final deve estar no formato dia-mês-ano (ex: 27-03-2026).',
            'sellerID.exists' => 'O vendedor selecionado não existe.',
        ];
    }
}
