<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class CreateDifferencePaymentRequest extends FormRequest
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
            'differenceDeliveryData.freight' => 'required|boolean',
            'differenceDeliveryData.freightValue' => 'required|numeric|min:0',
            'differenceDeliveryData.cep' => 'nullable|numeric',
            'differenceDeliveryData.state' => 'nullable|string|max:20',
            'differenceDeliveryData.city' => 'nullable|string|max:50',
            'differenceDeliveryData.neighborhood' => 'nullable|string|max:50',
            'differenceDeliveryData.address' => 'nullable|string|max:100',
            'differenceDeliveryData.numberAddress' => 'nullable|numeric',
            'differenceDeliveryData.complement' => 'nullable|string|max:100',
            'differenceDeliveryData.recipientName' => 'nullable|string|min:3|max:100',
            'differenceDeliveryData.recipientPhone' => 'nullable|string|max:20',
            'differenceDeliveryData.observation' => 'nullable|string|max:5000',
            'additionalDifferencePaymentData.saleID' => 'required|exists:sales,id',
            'additionalDifferencePaymentData.exchangeID' => 'required|exists:exchanges,id',
            'additionalDifferencePaymentData.change' => 'required|numeric|min:0',
            'additionalDifferencePaymentData.fees' => 'required|numeric|min:0',
            'additionalDifferencePaymentData.description' => 'nullable|string|max:1000',
            'differencePaymentData' => 'required|array',
            'differencePaymentData.*.paymentType' => 'required|string|exists:types_receipt,name',
            'differencePaymentData.*.value' => 'nullable|numeric|min:0',
            'differencePaymentData.*.receiptID' => 'nullable|exists:receipts,id',
            'differencePaymentData.*.installment' => 'nullable',
            'differencePaymentData.*.installment.value' => 'nullable|integer|min:1|max:12',
            'differencePaymentData.*.installment.amount' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'differenceDeliveryData.freightValue.required' => 'O valor do frete é obrigatório.',
            'differenceDeliveryData.freightValue.numeric' => 'O valor do frete deve ser numérico.',
            'differenceDeliveryData.freightValue.min' => 'O valor do frete não pode ser negativo.',
            'differenceDeliveryData.cep.numeric' => 'O CEP deve ser numérico.',
            'differenceDeliveryData.state.max' => 'O estado não pode exceder 20 caracteres.',
            'differenceDeliveryData.city.max' => 'A cidade não pode exceder 50 caracteres.',
            'differenceDeliveryData.neighborhood.max' => 'O bairro não pode exceder 50 caracteres.',
            'differenceDeliveryData.address.max' => 'O endereço não pode exceder 100 caracteres.',
            'differenceDeliveryData.numberAddress.numeric' => 'O número do endereço deve ser numérico.',
            'differenceDeliveryData.complement.max' => 'O complemento não pode exceder 100 caracteres.',
            'differenceDeliveryData.recipientName.min' => 'O nome do recebedor deve ter pelo menos 3 caracteres.',
            'differenceDeliveryData.recipientName.max' => 'O nome do recebedor não pode exceder 100 caracteres.',
            'differenceDeliveryData.recipientPhone.max' => 'O telefone do recebedor não pode exceder 20 caracteres.',
            'additionalDifferencePaymentData.saleID.required' => 'O ID da venda é obrigatório',
            'additionalDifferencePaymentData.saleID.exists' => 'O ID da venda não existe',
            'additionalDifferencePaymentData.exchangeID.required' => 'O ID do estorno é obrigatório',
            'additionalDifferencePaymentData.exchangeID.exists' => 'O ID do estorno informada não existe',
            'additionalDifferencePaymentData.change.required' => 'O troco do pagamento deve ser informado',
            'additionalDifferencePaymentData.change.numeric' => 'O troco do pagamento deve estar em formato numérico',
            'additionalDifferencePaymentData.change.min' => 'O troco do pagamento deve ser no mínimo 0',
            'additionalDifferencePaymentData.fees.required' => 'A tarifa do pagamento deve ser informado',
            'additionalDifferencePaymentData.fees.numeric' => 'A tarifa do pagamento deve estar em formato numérico',
            'additionalDifferencePaymentData.fees.min' => 'A tarifa do pagamento deve ser no mínimo 0',
            'additionalDifferencePaymentData.description.string' => 'A descrição deve ser uma string',
            'additionalDifferencePaymentData.description.max' => 'A descrição não pode ultrapassar mais de 1000 caracteres',
            'differencePaymentData.required' => 'Insira os dados do pagamento do estorno',
            'differencePaymentData.array' => 'Os dados do pagamento do estorno devem ser um array',
            'differencePaymentData.*.paymentType.required' => 'O tipo de pagamento é obrigatório',
            'differencePaymentData.*.paymentType.exists' => 'O tipo de pagamento selecionado não existe',
            'differencePaymentData.*.value.required' => 'Deve ser informado o valor',
            'differencePaymentData.*.receiptID.exists' => 'O ID do recebimento informado não existe',
            'differencePaymentData.*.installment.value.integer' => 'O número de parcelas deve ser um inteiro.',
            'differencePaymentData.*.installment.value.min' => 'O mínimo de parcelas é 1.',
            'differencePaymentData.*.installment.value.max' => 'O máximo de parcelas é 12.',
            'differencePaymentData.*.installment.amount.numeric' => 'O valor da parcela deve ser numérico.',
        ];
    }
}
