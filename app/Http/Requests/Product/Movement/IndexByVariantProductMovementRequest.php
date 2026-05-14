<?php

namespace App\Http\Requests\Product\Movement;

use Illuminate\Foundation\Http\FormRequest;

class IndexByVariantProductMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productVariantID' => 'required|exists:product_variants,id',
        ];
    }

    public function messages(): array
    {
        return [
            'productVariantID.required' => 'O ID da variante do produto é obrigatório.',
            'productVariantID.exists' => 'A variante do produto informada não existe.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
