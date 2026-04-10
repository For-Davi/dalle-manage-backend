<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class FilterSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => 'nullable|string|date_format:d/m/Y',
            'endDate' => 'nullable|string|date_format:d/m/Y',
            'status' => 'nullable|string|in:all,active,canceled',
            'client' => 'nullable|exists:clients,id',
            'seller' => 'nullable|exists:employees,id',
            'product' => 'nullable|exists:product_variants,id',
            'paymentType' => 'nullable|exists:types_receipt,id',
            'receipt' => 'nullable|exists:receipts,id',
            'minTotal' => 'nullable|numeric|min:0',
            'maxTotal' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.string' => 'A data inicial deve ser um texto.',
            'startDate.date_format' => 'A data inicial deve estar no formato dd/mm/aaaa.',
            'endDate.string' => 'A data final deve ser um texto.',
            'endDate.date_format' => 'A data final deve estar no formato dd/mm/aaaa.',
            'status.string' => 'O status deve ser um texto válido.',
            'status.in' => 'O status informado não é válido.',
            'client.exists' => 'O cliente selecionado não existe.',
            'seller.exists' => 'O vendedor selecionado não existe.',
            'product.exists' => 'O produto selecionado não existe.',
            'paymentType.exists' => 'O tipo de pagamento selecionado não existe.',
            'receipt.exists' => 'O recebimento selecionado não existe.',
            'minTotal.numeric' => 'O total mínimo deve ser um número.',
            'minTotal.min' => 'O total mínimo deve ser maior ou igual a 0.',
            'maxTotal.numeric' => 'O total máximo deve ser um número.',
            'maxTotal.min' => 'O total máximo deve ser maior ou igual a 0.',
        ];
    }
}
