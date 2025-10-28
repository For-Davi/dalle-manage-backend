<?php

namespace App\Http\Requests\Supplier\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplierID' => 'required|exists:suppliers,id',
            'dateDeliveryExpected' => 'nullable|date_format:d/m/Y',
            'dateIssue' => 'nullable|date_format:d/m/Y',
            'orderNumber' => 'nullable|string',
            'observation' => 'nullable|string',

            'items' => 'required|array',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.total_cost' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplierID.required' => 'O fornecedor é obrigatório.',
            'supplierID.exists' => 'O fornecedor informado é inválido.',

            'dateDeliveryExpected.date_format' => 'A data de entrega esperada deve estar no formato dd/mm/aaaa.',
            'dateIssue.date_format' => 'A data de emissão deve estar no formato dd/mm/aaaa.',

            'items.required' => 'É obrigatório informar pelo menos um item.',
            'items.array' => 'Os itens devem estar em formato de lista.',

            'items.*.product_variant_id.required' => 'O item deve conter uma variante de produto.',
            'items.*.product_variant_id.exists' => 'A variante de produto informada é inválida.',

            'items.*.quantity_requested.required' => 'A quantidade solicitada do item é obrigatória.',
            'items.*.quantity_requested.integer' => 'A quantidade solicitada deve ser um número inteiro.',
            'items.*.quantity_requested.min' => 'A quantidade solicitada deve ser no mínimo 1.',

            'items.*.unit_cost.required' => 'O custo unitário do item é obrigatório.',
            'items.*.unit_cost.numeric' => 'O custo unitário deve ser um número.',
            'items.*.unit_cost.min' => 'O custo unitário não pode ser negativo.',

            'items.*.total_cost.required' => 'O custo total do item é obrigatório.',
            'items.*.total_cost.numeric' => 'O custo total deve ser um número.',
            'items.*.total_cost.min' => 'O custo total não pode ser negativo.',
        ];
    }
}
