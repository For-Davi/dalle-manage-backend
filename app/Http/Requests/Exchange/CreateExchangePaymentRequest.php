<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class CreateExchangePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('exchangeData')) {
            $this->merge([
                'exchangeData' => collect($this->input('exchangeData', []))
                    ->map(fn ($e) => is_string($e) ? json_decode($e, true) : $e)
                    ->toArray(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'additionalExchangePaymentData.saleID' => 'required|exists:sales,id',
            'additionalExchangePaymentData.exchangeID' => 'required|exists:exchanges,id',
            'additionalExchangePaymentData.change' => 'required|numeric|min:0',
            'additionalExchangePaymentData.description' => 'nullable|string|max:1000',
            'exchangePaymentData' => 'required|array',
            'exchangePaymentData.*.paymentType' => 'required|string|exists:types_receipt,name',
            'exchangePaymentData.*.value' => 'nullable|numeric|min:0',
            'exchangePaymentData.*.receiptID' => 'required|exists:receipts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'additionalExchangePaymentData.saleID.required' => 'O ID da venda é obrigatório',
            'additionalExchangePaymentData.saleID.exists' => 'O ID da venda não existe',
            'additionalExchangePaymentData.exchangeID.required' => 'O ID do estorno é obrigatório',
            'additionalExchangePaymentData.exchangeID.exists' => 'O ID do estorno informada não existe',
            'additionalExchangePaymentData.change.required' => 'O troco do pagamento deve ser informado',
            'additionalExchangePaymentData.change.numeric' => 'O troco do pagamento deve estar em formato numérico',
            'additionalExchangePaymentData.change.min' => 'O troco do pagamento deve ser no mínimo 0',
            'additionalExchangePaymentData.description.string' => 'A descrição deve ser uma string',
            'additionalExchangePaymentData.description.max' => 'A descrição não pode ultrapassar mais de 1000 caracteres',
            'exchangePaymentData.required' => 'Insira os dados do pagamento do estorno',
            'exchangePaymentData.array' => 'Os dados do pagamento do estorno devem ser um array',
            'exchangePaymentData.*.paymentType.required' => 'O tipo de pagamento é obrigatório',
            'exchangePaymentData.*.paymentType.exists' => 'O tipo de pagamento selecionado não existe',
            'exchangePaymentData.*.value.required' => 'Deve ser informado o valor',
            'exchangePaymentData.*.receiptID.required' => 'O ID do recebimento deve ser informado',
            'exchangePaymentData.*.receiptID.exists' => 'O ID do recebimento informado não existe',
        ];
    }
}
