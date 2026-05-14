<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class CheckSaleProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.product_variant_id' => 'required|integer|exists:product_variants,id',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'A lista de produtos é obrigatória.',
            'products.array' => 'Os produtos devem ser enviados como uma lista.',
            'products.*.product_id.required' => 'O ID do produto é obrigatório.',
            'products.*.product_id.integer' => 'O ID do produto deve ser um número inteiro.',
            'products.*.product_id.exists' => 'O produto informado não existe.',
            'products.*.product_variant_id.required' => 'O ID da variante do produto é obrigatório.',
            'products.*.product_variant_id.integer' => 'O ID da variante deve ser um número inteiro.',
            'products.*.product_variant_id.exists' => 'A variante do produto informada não existe.',
            'products.*.discount.numeric' => 'O desconto deve ser um valor numérico.',
            'products.*.discount.min' => 'O desconto não pode ser negativo.',
            'products.*.discount.max' => 'O desconto não pode ser maior que 100%.',
        ];
    }
}
