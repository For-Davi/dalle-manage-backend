<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\MaxDateRange;
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
            'startDate' => [
                'nullable',
                'required_with:endDate',
                'date_format:m-Y',
            ],
            'endDate' => [
                'nullable',
                'required_with:startDate',
                'date_format:m-Y',
                new MaxDateRange(4),
            ],
            'seller' => 'nullable|exists:employees,id',
            'category' => 'nullable|exists:product_categories,id',
            'product' => 'nullable|exists:products,name',
            'typeReceipt' => 'nullable|exists:types_receipt,id',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.required_with' => 'A data inicial é obrigatória quando a data final é informada',
            'startDate.date_format' => 'A data inicial deve estar no formato mm-yyyy',
            'endDate.required_with' => 'A data final é obrigatória quando a data inicial é informada',
            'endDate.date_format' => 'A data final deve estar no formato mm-yyyy',
            'seller.exists' => 'O vendedor selecionado não existe',
            'category.exists' => 'A categoria selecionada não existe',
            'product.exists' => 'O produto selecionado não existe',
            'typeReceipt.exists' => 'O tipo de recebimento selecionado não existe',
        ];
    }
}
