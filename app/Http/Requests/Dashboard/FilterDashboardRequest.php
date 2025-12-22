<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class FilterDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => 'nullable|date_format:d/m/Y',
            'endDate' => 'nullable|date_format:d/m/Y',
            'seller' => 'nullable|exists:employees,id',
            'category' => 'nullable|exists:product_categories,id',
            'product' => 'nullable|exists:products,name',
            'typeReceipt' => 'nullable|exists:types_receipt,id',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.date_format' => 'A data inicial deve estar no formato dd/mm/yyyy',
            'endDate.date_format' => 'A data final deve estar no formato dd/mm/yyyy',
            'seller.exists' => 'O vendedor selecionado não existe',
            'category.exists' => 'A categoria selecionada não existe',
            'product.exists' => 'O produto selecionado não existe',
            'typeReceipt.exists' => 'O tipo de recebimento selecionado não existe',
        ];
    }
}
