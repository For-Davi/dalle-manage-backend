<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GetInfoDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enterpriseID' => 'required|exists:enterprises,id',
        ];
    }

    public function messages(): array
    {
        return [
            'enterpriseID.required' => 'O ID da empresa é obrigatório.',
            'enterpriseID.exists' => 'O ID da empresa informado não existe.',
        ];
    }
}
