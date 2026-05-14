<?php

namespace App\Http\Requests\DalleAdm\Seller;

use Illuminate\Foundation\Http\FormRequest;

class FilterDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startPeriod' => 'nullable|date_format:m/Y',
            'endPeriod' => 'nullable|date_format:m/Y',
        ];
    }

    public function messages(): array
    {
        return [
            'startPeriod.date_format' => 'A data inicial deve estar no formato mês-ano (ex: 03/2026).',
            'endPeriod.date_format' => 'A data final deve estar no formato mês-ano (ex: 03/2026).',
        ];
    }
}
