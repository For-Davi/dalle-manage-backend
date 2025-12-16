<?php

namespace App\Http\Requests\Product\Movement;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|in:sell,buy,return,loss,transfer_in,transfer_out,adjustment_in,adjustment_out,production,internal_use',
            'type' => 'required|in:in,out',
            'documentNumber' => 'nullable|string|max:255',
            'lotNumber' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unitCost' => 'nullable|numeric|min:0',
            'totalCost' => 'nullable|numeric|min:0',
            'variantID' => 'required|exists:product_variants,id',
            'supplierID' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'O motivo do movimento é obrigatório.',
            'reason.in' => 'O motivo informado não é válido.',

            'type.required' => 'O tipo do movimento é obrigatório.',
            'type.in' => 'O tipo deve ser entrada (in) ou saída (out).',

            'documentNumber.string' => 'O número do documento deve ser um texto válido.',
            'documentNumber.max' => 'O número do documento não pode ultrapassar 255 caracteres.',

            'lotNumber.string' => 'O número do lote deve ser um texto válido.',
            'lotNumber.max' => 'O número do lote não pode ultrapassar 255 caracteres.',

            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.numeric' => 'A quantidade deve ser numérica.',
            'quantity.min' => 'A quantidade deve ser maior que zero.',

            'unitCost.numeric' => 'O custo unitário deve ser numérico.',
            'unitCost.min' => 'O custo unitário não pode ser negativo.',

            'totalCost.numeric' => 'O custo total deve ser numérico.',
            'totalCost.min' => 'O custo total não pode ser negativo.',

            'variantID.required' => 'O produto é obrigatório.',
            'variantID.exists' => 'O produto informado não existe.',

            'supplierID.exists' => 'O fornecedor informado não existe.',

            'description.string' => 'A descrição deve ser um texto válido.',
        ];
    }
}
