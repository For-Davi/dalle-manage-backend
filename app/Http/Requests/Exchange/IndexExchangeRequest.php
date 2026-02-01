<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class IndexExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleID' => 'required|exists:sales,id',
        ];
    }

    public function messages(): array
    {
        return [
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
