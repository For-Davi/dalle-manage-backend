<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CreatePartialDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {

        if ($this->has('deliveredProducts')) {
            $products = collect($this->input('deliveredProducts', []))
                ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                ->toArray();

            $this->merge([
                'deliveredProducts' => $products,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'deliveryID' => 'required|exists:sale_deliveries,id',
            'deliveredProducts' => 'required|array',
            'deliveredProducts.*.productVariantID' => 'required|exists:product_variants,id',
            'deliveredProducts.*.quantitySaled' => 'required|numeric|min:1',
            'deliveredProducts.*.quantityDelivered' => 'required|numeric',
            'status' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryID.required' => 'O ID da entrega é obrigatório.',
            'deliveryID.exists' => 'A entrega informada não existe no sistema.',
            'status.required' => 'O status da entrega é obrigatório.',
            'status.string' => 'O status deve ser um texto válido.',
            'deliveredProducts.required' => 'É necessário informar ao menos um produto.',
            'deliveredProducts.array' => 'A lista de produtos deve ser um array.',
            'deliveredProducts.min' => 'A lista deve conter pelo menos :min produto.',
            'deliveredProducts.*.productVariantID.required' => 'O ID da variante do produto é obrigatório.',
            'deliveredProducts.*.productVariantID.exists' => 'Um dos produtos selecionados não existe.',
            'deliveredProducts.*.quantitySaled.required' => 'A quantidade vendida é obrigatória.',
            'deliveredProducts.*.quantitySaled.numeric' => 'A quantidade vendida deve ser um número.',
            'deliveredProducts.*.quantitySaled.min' => 'A quantidade vendida deve ser pelo menos 1.',
            'deliveredProducts.*.quantityDelivered.required' => 'A quantidade entregue é obrigatória.',
            'deliveredProducts.*.quantityDelivered.numeric' => 'A quantidade entregue deve ser um número.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
