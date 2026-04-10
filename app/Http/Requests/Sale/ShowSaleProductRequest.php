<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class ShowSaleProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleID' => 'required|exists:sales,id',
            'notDelivered' => 'nullable|in:1,0'
        ];
    }

    public function messages(): array
    {
        return [
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
            'notDelivered.in' => 'O campo filtrar não entregues deve ser verdadeiro (1) ou falso (0).',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
