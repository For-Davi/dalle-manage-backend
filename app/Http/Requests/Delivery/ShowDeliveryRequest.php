<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class ShowDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryID' => 'required|exists:sale_deliveries,id',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryID.required' => 'O ID da entrega é obrigatório',
            'deliveryID.exists' => 'O ID da entrega informada não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
