<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryID' => 'required|exists:sale_deliveries,id',
            'deliveryStatus' => 'required|in:delivered,delivered_in_person|string',
            'status' => 'required|string',
            'deliveryGuyID' => 'nullable|exists:delivery_guys,id',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryID.required' => 'O ID da entrega é obrigatório',
            'deliveryID.exists' => 'O ID da entrega informada não existe',
            'deliveryStatus.required' => 'O status da entrega é obrigatório',
            'deliveryStatus.in' => 'O status da entrega deve ser Entregue ou Entregue pessoalmente',
            'deliveryStatus.string' => 'O status da entrega deve ser um texto',
            'status.required' => 'O status é requirido',
            'status.string' => 'O status deve ser uma string',
            'deliveryGuyID.exists' => 'O ID entregador informado não existe',
        ];
    }
}
