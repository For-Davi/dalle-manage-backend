<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

class DeleteReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleID' => 'required|exists:sales,id',
            'returnID' => 'required|exists:returns,id',
        ];
    }

    public function messages(): array
    {
        return [
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
            'returnID.required' => 'O ID da devolução é obrigatório',
            'returnID.exists' => 'O ID da devolução informada não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
