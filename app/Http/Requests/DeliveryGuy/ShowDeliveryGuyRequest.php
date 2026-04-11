<?php

namespace App\Http\Requests\DeliveryGuy;

use Illuminate\Foundation\Http\FormRequest;

class ShowDeliveryGuyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryGuyID' => 'required|exists:delivery_guys,id',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryGuyID.required' => 'O ID do entregador é obrigatório',
            'deliveryGuyID.exists' => 'O ID do entregador informado não existe',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
