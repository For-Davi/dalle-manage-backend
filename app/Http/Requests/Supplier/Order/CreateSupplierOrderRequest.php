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
            'items.*.productVariantID' => 'required|exists:product_variants,id',
            'items.*.quantityRequested' => 'required|integer|min:1',
            'items.*.unitCost' => 'required|numeric|min:0',
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

            'items.*.productVariantID.required' => 'O item deve conter uma variante de produto.',
            'items.*.productVariantID.exists' => 'A variante de produto informada é inválida.',

            'items.*.quantityRequested.required' => 'A quantidade solicitada do item é obrigatória.',
            'items.*.quantityRequested.integer' => 'A quantidade solicitada deve ser um número inteiro.',
            'items.*.quantityRequested.min' => 'A quantidade solicitada deve ser no mínimo 1.',

            'items.*.unitCost.required' => 'O custo unitário do item é obrigatório.',
            'items.*.unitCost.numeric' => 'O custo unitário deve ser um número.',
            'items.*.unitCost.min' => 'O custo unitário não pode ser negativo.',

        ];
    }
}
