<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('returnData')) {
            $this->merge([
                'returnData' => collect($this->input('returnData', []))
                    ->map(function ($return) {
                        return [
                            'reason' => $return['reason'] ?? null,
                            'description' => $return['description'] ?? null,
                            'products' => collect($return['products'] ?? [])
                                ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                                ->toArray(),
                        ];
                    })
                    ->toArray(),
            ]);
        }
        if ($this->has('exchangeProducts')) {
            $this->merge([
                'exchangeProducts' => collect($this->input('exchangeProducts', []))
                    ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                    ->toArray(),
            ]);
        }
        if ($this->has('paymentData.paymentExchangeOrDifferenceData.payment')) {
            $this->merge([
                'paymentData.paymentExchangeOrDifferenceData' => array_merge($this->input('paymentData.paymentExchangeOrDifferenceData', []), [
                    'payment' => collect($this->input('paymentData.paymentExchangeOrDifferenceData.payment', []))
                        ->map(function ($p) {
                            $p = is_string($p) ? json_decode($p, true) : $p;
                            if ($p['paymentType'] === 'CREDIT') {
                                $p['receiptID'] = null;
                            }

                            return $p;
                        })
                        ->toArray(),
                ]),
            ]);
        }
        if ($this->has('paymentData.freightPaymentData.payment')) {
            $this->merge([
                'paymentData.freightPaymentData' => array_merge($this->input('paymentData.freightPaymentData', []), [
                    'payment' => collect($this->input('paymentData.freightPaymentData.payment', []))
                        ->map(function ($p) {
                            $p = is_string($p) ? json_decode($p, true) : $p;
                            if ($p['paymentType'] === 'CREDIT') {
                                $p['receiptID'] = null;
                            }

                            return $p;
                        })
                        ->toArray(),
                ]),
            ]);
        }
    }

    private function requiresReceiptID(): bool
    {
        if (! $this->mustHaveExchangeOrDifferencePayment()) {
            return false;
        }

        $payments = $this->input('paymentData.paymentExchangeOrDifferenceData.payment', []);

        foreach ($payments as $payment) {
            if (($payment['paymentType'] ?? null) !== 'CREDIT' && empty($payment['receiptID'])) {
                return true;
            }
        }

        return false;
    }

    private function mustHaveExchangeOrDifferencePayment(): bool
    {
        return
            ($this->input('exchangeData.exchangeValue') > 0 ||
             $this->input('exchangeData.differenceValue') > 0)
            && ! $this->input('exchangeData.generatesCredit');
    }

    private function mustHaveFreightPayment(): bool
    {
        return $this->input('paymentData.deliveryData.freight') && $this->input('paymentData.deliveryData.freightValue') > 0 && $this->input('exchangeData.differenceValue') === 0;
    }

    public function rules(): array
    {
        return [
            // VENDA
            'saleID' => 'required|exists:sales,id',

            // VINCULO DE DEVOLUÇÃO
            'returnID' => 'nullable|exists:returns,id',

            // VENDEDOR
            'sellerID' => 'nullable|exists:employees,id',

            // REGRAS DA DEVOLUÇÃO
            'returnData' => 'required|array',
            'returnData.*.reason' => 'required|string|in:defect,violated,out_of_standard,wrong_sent,delivery_delay,wrong_bought,dissatisfaction,duplicate_order,incompatible,regret,payment_issue,not_informed',
            'returnData.*.description' => 'nullable|string|max: 1000',
            'returnData.*.products' => 'required|array',
            'returnData.*.products.*.product_variant_id' => 'required|exists:product_variants,id',
            'returnData.*.products.*.product_name' => 'required|string',
            'returnData.*.products.*.product_sku' => 'nullable|string',
            'returnData.*.products.*.product_code' => 'nullable|numeric',
            'returnData.*.products.*.product_price' => 'required|numeric',
            'returnData.*.products.*.quantity' => 'required|numeric|min:0',
            'returnData.*.products.*.returnQuantity' => 'required|numeric|min:1',
            'returnData.*.products.*.color' => 'nullable|string',
            'returnData.*.products.*.color_name' => 'nullable|string',
            'returnData.*.products.*.total' => 'required|numeric',

            // REGRAS DO ESTORNO
            'exchangeData.generatesCredit' => 'required|in:1,0',
            'exchangeData.exchangeValue' => 'required|numeric',
            'exchangeData.differenceValue' => 'required|numeric',

            // REGRAS DE PRODUTOS DA TROCA
            'exchangeProducts' => 'nullable|array',
            'exchangeProducts.*.product_variant_id' => 'required|exists:product_variants,id',
            'exchangeProducts.*.name' => 'required|string',
            'exchangeProducts.*.price' => 'required|numeric',
            'exchangeProducts.*.offer' => 'nullable|numeric',
            'exchangeProducts.*.stock_quantity' => 'required|min:1',
            'exchangeProducts.*.sku' => 'nullable|string',
            'exchangeProducts.*.code' => 'required|numeric',
            'saleData.products.*.variant_active' => 'required|in:1',
            'exchangeProducts.*.quantity' => 'required|numeric|min:1',
            'exchangeProducts.*.color.name' => 'nullable|string',
            'exchangeProducts.*.color.hex_color_code' => 'nullable|string',

            // PAGAMENTO (DIFERENÇA OU ESTORNO)
            'paymentData.paymentExchangeOrDifferenceData' => [
                'nullable',
                'array',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],

            // DADOS DA ENTREGA
            'paymentData.deliveryData.freight' => 'required|boolean',
            'paymentData.deliveryData.freightValue' => 'required|numeric|min:0',
            'paymentData.deliveryData.cep' => 'nullable|numeric',
            'paymentData.deliveryData.state' => 'nullable|string|max:20',
            'paymentData.deliveryData.city' => 'nullable|string|max:50',
            'paymentData.deliveryData.neighborhood' => 'nullable|string|max:50',
            'paymentData.deliveryData.address' => 'nullable|string|max:100',
            'paymentData.deliveryData.numberAddress' => 'nullable|numeric',
            'paymentData.deliveryData.complement' => 'nullable|string|max:100',
            'paymentData.deliveryData.recipientName' => 'nullable|string|min:3|max:100',
            'paymentData.deliveryData.recipientPhone' => 'nullable|string|max:20',
            'paymentData.deliveryData.observation' => 'nullable|string|max:5000',

            // PAGAMENTO DA DIFERENÇA OU DO ESTORNO
            'paymentData.paymentExchangeOrDifferenceData.change' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],
            'paymentData.paymentExchangeOrDifferenceData.fees' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],
            'paymentData.paymentExchangeOrDifferenceData.payment' => [
                'nullable',
                'array',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],
            'paymentData.paymentExchangeOrDifferenceData.payment.*.paymentType' => [
                'nullable',
                'string',
                'exists:types_receipt,name',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],
            'paymentData.paymentExchangeOrDifferenceData.payment.*.value' => 'nullable|numeric|min:0',
            'paymentData.paymentExchangeOrDifferenceData.payment.*.receiptID' => [
                'nullable',
                'exists:receipts,id',
                Rule::requiredIf(fn () => $this->requiresReceiptID()),
            ],
            'paymentData.paymentExchangeOrDifferenceData.payment.*.installment' => 'nullable',
            'paymentData.paymentExchangeOrDifferenceData.payment.*.installment.value' => 'nullable|integer|min:1|max:12',
            'paymentData.paymentExchangeOrDifferenceData.payment.*.installment.amount' => 'nullable|numeric',

            // PAGAMENTO DO FRETE DO CLIENTE
            'paymentData.freightPaymentData' => [
                'nullable',
                'array',
                Rule::requiredIf(fn () => $this->mustHaveExchangeOrDifferencePayment()),
            ],
            'paymentData.freightPaymentData.change' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $this->mustHaveFreightPayment()),
            ],
            'paymentData.freightPaymentData.fees' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $this->mustHaveFreightPayment()),
            ],
            'paymentData.freightPaymentData.payment' => [
                'nullable',
                'array',
                Rule::requiredIf(fn () => $this->mustHaveFreightPayment()),
            ],
            'paymentData.freightPaymentData.payment.*.paymentType' => [
                'nullable',
                'string',
                'exists:types_receipt,name',
                Rule::requiredIf(fn () => $this->mustHaveFreightPayment()),
            ],
            'paymentData.freightPaymentData.payment.*.value' => 'nullable|numeric|min:0',
            'paymentData.freightPaymentData.payment.*.receiptID' => [
                'nullable',
                'exists:receipts,id',
                Rule::requiredIf(fn () => $this->mustHaveFreightPayment()),
            ],
            'paymentData.freightPaymentData.payment.*.installment' => 'nullable',
            'paymentData.freightPaymentData.payment.*.installment.value' => 'nullable|integer|min:1|max:12',
            'paymentData.freightPaymentData.payment.*.installment.amount' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            // VENDA
            'saleID.required' => 'É obrigatório o id da venda',
            'saleID.exists' => 'O id da venda informada não existe',

            // VINCULO DE DEVOLUÇÃO
            'returnID.exsists' => 'O id da devolução informada não existe',

            // VENDEDOR
            'saleID.exsists' => 'O id do vendedor informado não existe',

            // DEVOLUÇÃO
            'returnData.required' => 'É obrigatório informar os itens que foram devolvidos.',
            'returnData.array' => 'Os itens devolvidos devem ser enviados em formato de lista.',
            'returnData.*.reason.required' => 'Informe o motivo da devolução.',
            'returnData.*.reason.string' => 'O motivo da devolução deve ser um texto válido.',
            'returnData.*.reason.in' => 'O motivo da devolução informado não é válido.',
            'returnData.*.description.string' => 'A descrição da devolução deve ser um texto válido.',
            'returnData.*.description.max' => 'A descrição da devolução não pode ultrapassar 1000 caracteres.',
            'returnData.*.products.required' => 'É obrigatório informar os produtos da devolução.',
            'returnData.*.products.array' => 'Os produtos da devolução devem ser enviados em formato de lista.',
            'returnData.*.products.*.product_variant_id.required' => 'Informe o produto devolvido.',
            'returnData.*.products.*.product_variant_id.exists' => 'O produto devolvido informado não é válido.',
            'returnData.*.products.*.product_name.required' => 'Informe o nome do produto devolvido.',
            'returnData.*.products.*.product_name.string' => 'O nome do produto devolvido deve ser um texto válido.',
            'returnData.*.products.*.product_sku.string' => 'O SKU do produto devolvido deve ser um texto válido.',
            'returnData.*.products.*.product_code.numeric' => 'O código do produto devolvido deve ser um número válido.',
            'returnData.*.products.*.product_price.required' => 'Informe o valor do produto devolvido.',
            'returnData.*.products.*.product_price.numeric' => 'O valor do produto devolvido deve ser numérico.',
            'returnData.*.products.*.quantity.required' => 'Informe a quantidade do produto devolvido.',
            'returnData.*.products.*.quantity.numeric' => 'A quantidade em estoque do produto devolvido deve ser numérica.',
            'returnData.*.products.*.quantity.min' => 'A quantidade em estoque do produto devolvido deve ser no mínimo 1.',
            'returnData.*.products.*.returnQuantity.required' => 'Informe a quantidade do produto devolvido.',
            'returnData.*.products.*.returnQuantity.numeric' => 'A quantidade do produto a ser devolvido deve ser numérica.',
            'returnData.*.products.*.returnQuantity.min' => 'A quantidade do produto devolvido deve ser no mínimo 1.',
            'returnData.*.products.*.color.string' => 'A cor do produto devolvido deve ser um texto válido.',
            'returnData.*.products.*.color_name.string' => 'O nome da cor do produto devolvido deve ser um texto válido.',
            'returnData.*.products.*.total.required' => 'Informe o valor total do produto devolvido.',
            'returnData.*.products.*.total.numeric' => 'O valor total do produto devolvido deve ser numérico.',

            // ESTORNO
            'exchangeData.exchangeValue.required' => 'É obrigatório informar o valor do estorno.',
            'exchangeData.exchangeValue.numeric' => 'O valor do estorno deve ser numérico.',
            'exchangeData.differenceValue.required' => 'É obrigatório informar o valor da diferença.',
            'exchangeData.differenceValue.numeric' => 'O valor da diferença deve ser numérico.',

            // ITENS DE TROCA
            'exchangeProducts.array' => 'Os produtos da troca devem ser enviados em formato de lista.',
            'exchangeProducts.*.product_variant_id.required' => 'Informe o produto da troca.',
            'exchangeProducts.*.product_variant_id.exists' => 'O produto da troca informado não é válido.',
            'exchangeProducts.*.name.required' => 'Informe o nome do produto da troca.',
            'exchangeProducts.*.name.string' => 'O nome do produto da troca deve ser um texto válido.',
            'exchangeProducts.*.price.required' => 'Informe o valor do produto da troca.',
            'exchangeProducts.*.price.numeric' => 'O valor do produto da troca deve ser numérico.',
            'exchangeProducts.*.offer.numeric' => 'O valor da oferta deve ser numérico.',
            'exchangeProducts.*.stock_quantity.required' => 'Informe o estoque disponível do produto da troca.',
            'exchangeProducts.*.stock_quantity.min' => 'O estoque do produto da troca deve ser no mínimo 1.',
            'exchangeProducts.*.sku.string' => 'O SKU do produto da troca deve ser um texto válido.',
            'exchangeProducts.*.code.required' => 'Informe o código do produto da troca.',
            'exchangeProducts.*.code.numeric' => 'O código do produto da troca deve ser numérico.',
            'saleData.products.*.variant_active.required' => 'O produto selecionado não está ativo.',
            'saleData.products.*.variant_active.in' => 'O produto selecionado não está ativo.',
            'exchangeProducts.*.quantity.required' => 'Informe a quantidade do produto da troca.',
            'exchangeProducts.*.quantity.numeric' => 'A quantidade do produto da troca deve ser numérica.',
            'exchangeProducts.*.quantity.min' => 'A quantidade do produto da troca deve ser no mínimo 1.',
            'exchangeProducts.*.color.name.string' => 'O nome da cor deve ser um texto válido.',
            'exchangeProducts.*.color.hex_color_code.string' => 'O código hexadecimal da cor deve ser um texto válido.',

            // ENTREGA
            'paymentData.deliveryData.freight.required' => 'Informe se a entrega possui frete.',
            'paymentData.deliveryData.freight.boolean' => 'O campo de frete deve ser verdadeiro ou falso.',
            'paymentData.deliveryData.freightValue.required' => 'Informe o valor do frete.',
            'paymentData.deliveryData.freightValue.numeric' => 'O valor do frete deve ser numérico.',
            'paymentData.deliveryData.freightValue.min' => 'O valor do frete não pode ser negativo.',
            'paymentData.deliveryData.cep.numeric' => 'O CEP deve ser numérico.',
            'paymentData.deliveryData.state.string' => 'O estado deve ser um texto válido.',
            'paymentData.deliveryData.state.max' => 'O estado não pode ultrapassar 20 caracteres.',
            'paymentData.deliveryData.city.string' => 'A cidade deve ser um texto válido.',
            'paymentData.deliveryData.city.max' => 'A cidade não pode ultrapassar 50 caracteres.',
            'paymentData.deliveryData.neighborhood.string' => 'O bairro deve ser um texto válido.',
            'paymentData.deliveryData.neighborhood.max' => 'O bairro não pode ultrapassar 50 caracteres.',
            'paymentData.deliveryData.address.string' => 'O endereço deve ser um texto válido.',
            'paymentData.deliveryData.address.max' => 'O endereço não pode ultrapassar 100 caracteres.',
            'paymentData.deliveryData.numberAddress.numeric' => 'O número do endereço deve ser numérico.',
            'paymentData.deliveryData.complement.string' => 'O complemento deve ser um texto válido.',
            'paymentData.deliveryData.complement.max' => 'O complemento não pode ultrapassar 100 caracteres.',
            'paymentData.deliveryData.recipientName.string' => 'O nome do destinatário deve ser um texto válido.',
            'paymentData.deliveryData.recipientName.min' => 'O nome do destinatário deve ter no mínimo 3 caracteres.',
            'paymentData.deliveryData.recipientName.max' => 'O nome do destinatário não pode ultrapassar 100 caracteres.',
            'paymentData.deliveryData.recipientPhone.string' => 'O telefone do destinatário deve ser um texto válido.',
            'paymentData.deliveryData.recipientPhone.max' => 'O telefone não pode ultrapassar 20 caracteres.',
            'paymentData.deliveryData.observation.string' => 'A observação deve ser um texto válido.',
            'paymentData.deliveryData.observation.max' => 'A observação não pode ultrapassar 5000 caracteres.',

            // PAGAMENTO
            'paymentData.paymentExchangeOrDifferenceData.payment.required' => 'Informe os dados de pagamento.',
            'paymentData.paymentExchangeOrDifferenceData.payment.array' => 'Os dados de pagamento devem ser um array.',
            'paymentData.paymentExchangeOrDifferenceData.change.required' => 'Informe o valor do troco.',
            'paymentData.paymentExchangeOrDifferenceData.change.numeric' => 'O valor do troco deve ser numérico.',
            'paymentData.paymentExchangeOrDifferenceData.change.min' => 'O valor do troco deve ser no mínimo 0.',
            'paymentData.paymentExchangeOrDifferenceData.fees.required' => 'Informe o valor das tarifas.',
            'paymentData.paymentExchangeOrDifferenceData.fees.numeric' => 'O valor das tarifas deve ser numérico.',
            'paymentData.paymentExchangeOrDifferenceData.fees.min' => 'O valor das tarifas deve ser no mínimo 0.',
            'paymentData.paymentExchangeOrDifferenceData.*.paymentType.required' => 'Informe o tipo de pagamento.',
            'paymentData.paymentExchangeOrDifferenceData.*.paymentType.string' => 'O tipo de pagamento deve ser um texto válido.',
            'paymentData.paymentExchangeOrDifferenceData.*.paymentType.exists' => 'O tipo de pagamento informado não é existe.',
            'paymentData.paymentExchangeOrDifferenceData.*.value.numeric' => 'O valor do pagamento deve ser numérico.',
            'paymentData.paymentExchangeOrDifferenceData.*.value.min' => 'O valor do pagamento deve ser no mínimo 0.',
            'paymentData.paymentExchangeOrDifferenceData.*.receiptID.required' => 'Informe o identificador do recebimento.',
            'paymentData.paymentExchangeOrDifferenceData.*.receiptID.exists' => 'O identificador do recebimento informado não é válido.',
            'paymentData.paymentExchangeOrDifferenceData.*.installment.value.integer' => 'O número de parcelas deve ser um inteiro.',
            'paymentData.paymentExchangeOrDifferenceData.*.installment.value.min' => 'O mínimo de parcelas é 1.',
            'paymentData.paymentExchangeOrDifferenceData.*.installment.value.max' => 'O máximo de parcelas é 12.',
            'paymentData.paymentExchangeOrDifferenceData.*.installment.amount.numeric' => 'O valor da parcela deve ser numérico.',

            // PAGAMENTO DO FRETE
            'paymentData.freightPaymentData.payment.required' => 'Informe os dados de pagamento do frete.',
            'paymentData.freightPaymentData.payment.array' => 'Os dados de pagamento do frete devem ser um array.',
            'paymentData.freightPaymentData.change.required' => 'Informe o valor do troco do frete.',
            'paymentData.freightPaymentData.change.numeric' => 'O valor do troco do frete deve ser numérico.',
            'paymentData.freightPaymentData.change.min' => 'O valor do troco do frete deve ser no mínimo 0.',
            'paymentData.freightPaymentData.fees.required' => 'Informe o valor das tarifas do frete.',
            'paymentData.freightPaymentData.fees.numeric' => 'O valor das tarifas do frete deve ser numérico.',
            'paymentData.freightPaymentData.fees.min' => 'O valor das tarifas do frete deve ser no mínimo 0.',
            'paymentData.freightPaymentData.*.paymentType.required' => 'Informe o tipo de pagamento do frete.',
            'paymentData.freightPaymentData.*.paymentType.string' => 'O tipo de pagamento do frete deve ser um texto válido.',
            'paymentData.freightPaymentData.*.paymentType.exists' => 'O tipo de pagamento do frete informado não é existe.',
            'paymentData.freightPaymentData.*.value.numeric' => 'O valor do pagamento do frete deve ser numérico.',
            'paymentData.freightPaymentData.*.value.min' => 'O valor do pagamento do frete deve ser no mínimo 0.',
            'paymentData.freightPaymentData.*.receiptID.required' => 'Informe o identificador do recebimento do frete.',
            'paymentData.freightPaymentData.*.receiptID.exists' => 'O identificador do recebimento do frete não é válido.',
            'paymentData.freightPaymentData.*.installment.value.integer' => 'O número de parcelas do frete deve ser um inteiro.',
            'paymentData.freightPaymentData.*.installment.value.min' => 'O mínimo de parcelas do frete é 1.',
            'paymentData.freightPaymentData.*.installment.value.max' => 'O máximo de parcelas do frete é 12.',
            'paymentData.freightPaymentData.*.installment.amount.numeric' => 'O valor da parcela do frete deve ser numérico.',
        ];
    }
}
