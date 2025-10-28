<?php

namespace App\Http\Requests\Supplier\Order;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSupplierOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orderID' => 'required|exists:supplier_orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'orderID.required' => 'O ID do pedido é obrigatória.',
            'orderID.exists' => 'O pedido informado não existe.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
