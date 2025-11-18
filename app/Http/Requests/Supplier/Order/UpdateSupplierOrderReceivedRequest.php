<?php

namespace App\Http\Requests\Supplier\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierOrderReceivedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dateReceived' => 'required|date_format:d/m/Y',
            'items.*.id' => 'nullable|exists:supplier_order_items,id',
            'items.*.received' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'dateReceived.required' => 'A data de recebimento é obrigatória para todos os itens.',
            'dateReceived.date_format' => 'A data de recebimento deve estar no formato (dd/mm/aaaa).',
            'items.*.id.exists' => 'O item do pedido selecionado é inválido.',
            'items.*.received.required' => 'A quantidade recebida é obrigatória para todos os itens.',
            'items.*.received.numeric' => 'A quantidade recebida deve ser um valor numérico.',
            'items.*.received.min' => 'A quantidade recebida deve ser no mínimo 1.',
        ];
    }
}
