<?php

namespace App\Http\Requests;

use App\Enums\Supplier\Order\SupplierOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSupplierOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:supplier_orders,id',
            'status' => ['required', new Enum(SupplierOrderStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status é obrigatório.',
            'status' => 'Status inválido.',
        ];
    }
}
