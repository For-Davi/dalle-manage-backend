<?php

namespace App\Http\Requests\Supplier\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:supplier_orders,id',
            'items' => 'required|array',
            'items.*.id' => 'nullable|exists:supplier_order_items,id',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.total_cost' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O pedido é obrigatório para atualização.',
            'id.exists' => 'O pedido informado não foi encontrado.',
            'items.required' => 'É obrigatório informar pelo menos um item.',
            'items.array' => 'Os itens devem estar em formato de lista.',
            'items.*.id.exists' => 'Um dos itens informados para atualização não existe no pedido.',
            'items.*.product_variant_id.required' => 'A variante de produto é obrigatória em todos os itens.',
            'items.*.product_variant_id.exists' => 'Uma das variantes de produto informadas é inválida.',
            'items.*.quantity_requested.required' => 'A quantidade solicitada é obrigatória.',
            'items.*.quantity_requested.integer' => 'A quantidade solicitada deve ser um número inteiro.',
            'items.*.quantity_requested.min' => 'A quantidade solicitada deve ser no mínimo 1.',
            'items.*.unit_cost.required' => 'O custo unitário é obrigatório.',
            'items.*.unit_cost.numeric' => 'O custo unitário deve ser numérico.',
            'items.*.unit_cost.min' => 'O custo unitário não pode ser negativo.',
            'items.*.total_cost.required' => 'O custo total é obrigatório.',
            'items.*.total_cost.numeric' => 'O custo total deve ser numérico.',
            'items.*.total_cost.min' => 'O custo total não pode ser negativo.',
        ];
    }
}
