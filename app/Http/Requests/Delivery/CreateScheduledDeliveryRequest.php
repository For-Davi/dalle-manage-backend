<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduledDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryID' => 'required|exists:sale_deliveries,id',
            'schedule' => 'required|string|date_format:d/m/Y',
            'deliveryGuyID' => 'required|exists:delivery_guys,id',
            'status' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryID.required' => 'O ID da entrega é obrigatório',
            'deliveryID.exists' => 'O ID da entrega informada não existe',
            'deliveryGuyID.required' => 'O ID do entregador é obrigatório',
            'deliveryGuyID.exists' => 'O ID da entrega informada não existe',
            'schedule.required' => 'A data de agendamento é obrigatório',
            'schedule.string' => 'O agendamento deve ser uma string',
            'schedule.date_format' => 'O agendamento deve estar no formato d/m/Y',
            'status.required' => 'O status é requirido',
            'status.string' => 'O status deve ser uma string',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
