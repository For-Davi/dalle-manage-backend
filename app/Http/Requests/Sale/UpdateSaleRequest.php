<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saleID' => 'required|exists:sales,id',
            'reason' => 'nullable|string|in:not_informed,regret,delivery_delay,wrong_product,out_of_stock,payment_failure,mistaken_purchase,duplicate_sale,incorrect_data,fraud,customer_restriction',
            'description' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'saleID.required' => 'O ID da venda é obrigatório',
            'saleID.exists' => 'O ID da venda informada não existe',
            'reason.in' => 'Insira um motivo de cancelamento válido',
            'reason.string' => 'O motivo do cancelamento deve ser uma string',
            'description.string' => 'A descrição deve ser uma string',
            'description.max' => 'A descrição não pode exceder 5000 caracteres',
        ];
    }
}
