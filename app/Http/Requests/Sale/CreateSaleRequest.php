<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class CreateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {

        if ($this->has('saleData.products')) {
            $this->merge([
                'saleData' => array_merge($this->input('saleData', []), [
                    'products' => collect($this->input('saleData.products', []))
                        ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                        ->toArray(),
                ]),
            ]);
        }
        if ($this->has('paymentData.payment')) {
            $this->merge([
                'paymentData' => array_merge($this->input('paymentData', []), [
                    'payment' => collect($this->input('paymentData.payment', []))
                        ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                        ->toArray(),
                ]),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // REGRAS DO CLIENTE
            'clientData' => 'nullable|array',
            'clientData.id' => 'required_with:clientData|exists:clients,id',
            'clientData.name' => 'required_with:clientData|string|min:1|max:100',
            'clientData.email' => 'nullable|email|max:100',
            'clientData.dateBirthday' => 'nullable|string',
            'clientData.cpf' => 'nullable|numeric',
            'clientData.cnpj' => 'nullable|numeric',
            'clientData.stateRegistration' => 'nullable|string|max:20',
            'clientData.municipalRegistration' => 'nullable|string|max:20',
            'clientData.phone' => 'nullable|string|max:20',
            'clientData.country' => 'nullable|string|max:50',
            'clientData.state' => 'nullable|string|max:20',
            'clientData.city' => 'nullable|string|max:50',
            'clientData.cep' => 'nullable|numeric',
            'clientData.neighborhood' => 'nullable|string|max:50',
            'clientData.address' => 'nullable|string|max:100',
            'clientData.number' => 'nullable|numeric',
            'clientData.complement' => 'nullable|string|max:100',
            'clientData.description' => 'nullable|string|max:500',
            'clientData.sex' => 'nullable|in:M,F',

            // REGRAS DA VENDA
            'saleData.totalPrice' => 'required|numeric|min:0.01',
            'saleData.products' => 'required|array',
            'saleData.products.*.productVariantID' => 'required|exists:product_variants,id',
            'saleData.products.*.newQuantity' => 'required|min:1',
            'saleData.products.*.variantActive' => 'required|in:1',

            // REGRAS DA ENTREGA
            'deliveryData.freight' => 'required|boolean',
            'deliveryData.freightValue' => 'required|numeric|min:0',
            'deliveryData.cep' => 'nullable|numeric',
            'deliveryData.state' => 'nullable|string|max:20',
            'deliveryData.city' => 'nullable|string|max:50',
            'deliveryData.neighborhood' => 'nullable|string|max:50',
            'deliveryData.address' => 'nullable|string|max:100',
            'deliveryData.numberAddress' => 'nullable|numeric',
            'deliveryData.complement' => 'nullable|string|max:100',
            'deliveryData.recipientName' => 'nullable|string|min:3|max:100',
            'deliveryData.recipientPhone' => 'nullable|string|max:20',
            'deliveryData.observation' => 'nullable|string|max:5000',

            // REGRAS DO PAGAMENTO
            'sellerID' => 'nullable|exists:employees,id',
            'paymentData.change' => 'required|numeric|min:0',
            'paymentData.fees' => 'required|numeric|min:0',
            'paymentData.couponID' => 'nullable',
            'paymentData.payment' => 'required|array',
            'paymentData.payment.*.paymentType' => 'required|string|exists:types_receipt,name',
            'paymentData.payment.*.value' => 'nullable|numeric|min:0',
            'paymentData.payment.*.receiptID' => 'required|exists:receipts,id',
            'paymentData.payment.*.installment' => 'nullable',
            'paymentData.payment.*.installment.value' => 'nullable|integer|min:1|max:12',
            'paymentData.payment.*.installment.amount' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            // Cliente
            'clientData.array' => 'Os dados do cliente devem ser um array',
            'clientData.id.exists' => 'O cliente selecionado não existe.',
            'clientData.id.required_with' => 'O ID do cliente é obrigatório quando os dados do cliente são informados.',
            'clientData.name.required_with' => 'O nome do cliente é obrigatório quando os dados do cliente são informados.',
            'clientData.name.string' => 'O nome do cliente deve ser um texto.',
            'clientData.name.min' => 'O nome do cliente deve ter pelo menos 1 caractere.',
            'clientData.name.max' => 'O nome do cliente não pode exceder 100 caracteres.',
            'clientData.email' => 'O e-mail do cliente deve ser válido.',
            'clientData.email.max' => 'O e-mail do cliente não pode exceder 100 caracteres.',
            'clientData.cpf.numeric' => 'O CPF deve ser um número.',
            'clientData.cnpj.numeric' => 'O CNPJ deve ser um número.',
            'clientData.stateRegistration.max' => 'A inscrição estadual não pode exceder 20 caracteres.',
            'clientData.municipalRegistration.max' => 'A inscrição municipal não pode exceder 20 caracteres.',
            'clientData.phone.max' => 'O telefone não pode exceder 20 caracteres.',
            'clientData.country.max' => 'O país não pode exceder 50 caracteres.',
            'clientData.state.max' => 'O estado não pode exceder 20 caracteres.',
            'clientData.city.max' => 'A cidade não pode exceder 50 caracteres.',
            'clientData.cep.numeric' => 'O CEP deve ser numérico.',
            'clientData.neighborhood.max' => 'O bairro não pode exceder 50 caracteres.',
            'clientData.address.max' => 'O endereço não pode exceder 100 caracteres.',
            'clientData.number.numeric' => 'O número deve ser numérico.',
            'clientData.complement.max' => 'O complemento não pode exceder 100 caracteres.',
            'clientData.description.max' => 'A descrição não pode exceder 500 caracteres.',
            'clientData.sex.in' => 'O sexo deve ser "M" ou "F".',

            // Venda
            'saleData.totalPrice.required' => 'O valor total da venda é obrigatório.',
            'saleData.totalPrice.numeric' => 'O valor total da venda deve ser numérico.',
            'saleData.totalPrice.min' => 'O valor total da venda deve ser no mínimo 0',
            'saleData.products.required' => 'É necessário enviar pelo menos um produto.',
            'saleData.products.array' => 'Os produtos devem ser um array.',
            'saleData.products.*.productVariantID.required' => 'O ID da variante do produto é obrigatório.',
            'saleData.products.*.productVariantID.exists' => 'O produto selecionado não existe.',
            'saleData.products.*.newQuantity.required' => 'A quantidade do produto é obrigatória.',
            'saleData.products.*.newQuantity.min' => 'A quantidade do produto deve ser no mínimo 1.',
            'saleData.products.*.variantActive.required' => 'Deve ser informado se a variante está ativa ou não',

            // Entrega
            'deliveryData.freightValue.required' => 'O valor do frete é obrigatório.',
            'deliveryData.freightValue.numeric' => 'O valor do frete deve ser numérico.',
            'deliveryData.freightValue.min' => 'O valor do frete não pode ser negativo.',
            'deliveryData.cep.numeric' => 'O CEP deve ser numérico.',
            'deliveryData.state.max' => 'O estado não pode exceder 20 caracteres.',
            'deliveryData.city.max' => 'A cidade não pode exceder 50 caracteres.',
            'deliveryData.neighborhood.max' => 'O bairro não pode exceder 50 caracteres.',
            'deliveryData.address.max' => 'O endereço não pode exceder 100 caracteres.',
            'deliveryData.numberAddress.numeric' => 'O número do endereço deve ser numérico.',
            'deliveryData.complement.max' => 'O complemento não pode exceder 100 caracteres.',
            'deliveryData.recipientName.min' => 'O nome do recebedor deve ter pelo menos 3 caracteres.',
            'deliveryData.recipientName.max' => 'O nome do recebedor não pode exceder 100 caracteres.',
            'deliveryData.recipientPhone.max' => 'O telefone do recebedor não pode exceder 20 caracteres.',

            // Pagamento
            'sellerID.exists' => 'O vendedor selecionado não existe.',
            'paymentData.change.required' => 'O valor do troco é obrigatório.',
            'paymentData.change.numeric' => 'O valor do troco deve ser numérico.',
            'paymentData.change.min' => 'O valor do troco não pode ser negativo.',
            'paymentData.fees.required' => 'O valor das tarifas é obrigatório.',
            'paymentData.fees.numeric' => 'O valor das tarifas deve ser numérico.',
            'paymentData.fees.min' => 'O valor das tarifas não pode ser negativo.',
            'paymentData.payment.required' => 'É necessário enviar pelo menos um pagamento.',
            'paymentData.payment.array' => 'O pagamento deve ser um array.',
            'paymentData.payment.*.paymentType.required' => 'O tipo de pagamento é obrigatório.',
            'paymentData.payment.*.paymentType.exists' => 'O tipo de pagamento selecionado não existe.',
            'paymentData.payment.*.value.numeric' => 'O valor do pagamento deve ser numérico.',
            'paymentData.payment.*.value.min' => 'O valor do pagamento não pode ser negativo.',
            'paymentData.payment.*.receiptID.required' => 'O recebimento é obrigatório.',
            'paymentData.payment.*.receiptID.exists' => 'O recebimento selecionado não existe.',
            'paymentData.payment.*.installment.value.integer' => 'O número de parcelas deve ser um inteiro.',
            'paymentData.payment.*.installment.value.min' => 'O mínimo de parcelas é 1.',
            'paymentData.payment.*.installment.value.max' => 'O máximo de parcelas é 12.',
            'paymentData.payment.*.installment.amount.numeric' => 'O valor da parcela deve ser numérico.',
        ];
    }
}
