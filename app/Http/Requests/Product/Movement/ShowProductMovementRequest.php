<?php

namespace App\Http\Requests\Product\Movement;

use Illuminate\Foundation\Http\FormRequest;

class ShowProductMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productMovementID' => 'required|exists:product_movements,id',
        ];
    }

    public function messages(): array
    {
        return [
            'productMovementID.required' => 'O ID da movimentação é obrigatório.',
            'productMovementID.exists' => 'A movimentação informada não existe.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
