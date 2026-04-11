<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class FilterDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string',
            'startDate' => 'nullable|string|date_format:d/m/Y',
            'endDate' => 'nullable|string|date_format:d/m/Y',
            'startScheduledDate' => 'nullable|string|date_format:d/m/Y',
            'endScheduledDate' => 'nullable|string|date_format:d/m/Y',
            'deliveryGuy' => 'nullable|exists:delivery_guys,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status deve ser informado.',
            'status.string' => 'O status deve ser um texto válido.',
            'startDate.string' => 'A data inicial deve ser um texto.',
            'startDate.date_format' => 'A data inicial deve estar no formato dd/mm/aaaa.',
            'endDate.string' => 'A data final deve ser um texto.',
            'endDate.date_format' => 'A data final deve estar no formato dd/mm/aaaa.',
            'startScheduledDate.string' => 'A data inicial do agendamento deve ser um texto.',
            'startScheduledDate.date_format' => 'A data inicial do agendamento deve estar no formato dd/mm/aaaa.',
            'endScheduledDate.string' => 'A data final do agendamento deve ser um texto.',
            'endScheduledDate.date_format' => 'A data final do agendamento deve estar no formato dd/mm/aaaa.',
            'deliveryGuy.exists' => 'O cliente selecionado não existe.',
        ];
    }
}
