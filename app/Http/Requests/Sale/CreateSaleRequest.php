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

        if ($this->has('dataSale.products')) {
            $this->merge([
                'dataSale' => array_merge($this->input('dataSale', []), [
                    'products' => collect($this->input('dataSale.products', []))
                        ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                        ->toArray(),
                ]),
            ]);
        }

        if ($this->has('payment')) {
            $this->merge([
                'payment' => collect($this->input('payment', []))
                    ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
                    ->toArray(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // REGRAS DO CLIENTE
            'clientData.id' => 'nullable|exists:clients,id',
            'clientData.name' => 'nullable|string|min:1|max:100',
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
            'sex' => 'nullable|in:M,F',

            // REGRAS DA VENDA
            'dataSale.totalPrice' => 'required|numeric|min:0',
            'dataSale.products' => 'required|array',
            'dataSale.products.*.product_variant_id' => 'required|exists:product_variants,id',
            'dataSale.products.*.newQuantity' => 'required|min:1',
            'dataSale.products.*.stock_quantity' => 'required|min:1',
            'dataSale.products.*.variant_active' => 'required|in:1',

            //REGRAS DA ENTREGA
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

            // REGRAS DO PAGAMENTO
            'sellerID' => 'nullable|exists:employees,id',
            'change' => 'required|numeric|min:0',
            'fees' => 'required|numeric|min:0',
            'couponID' => 'nullable',
            'payment' => 'required|array',
            'payment.*.paymentType' => 'required|string|exists:types_receipt,name',
            'payment.*.value' => 'nullable|numeric|min:0',
            'payment.*.receiptID' => 'required|exists:receipts,id',
            'payment.*.installment' => 'nullable',
            'payment.*.installment.value' => 'nullable|integer|min:2|max:12',
            'payment.*.installment.amount' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            // Cliente
            'clientData.id.exists' => 'O cliente selecionado não existe.',
            'clientData.name.string' => 'O nome do cliente deve ser um texto.',
            'clientData.name.min' => 'O nome do cliente deve ter pelo menos 1 caractere.',
            'clientData.name.max' => 'O nome do cliente não pode exceder 100 caracteres.',
            'clientData.email.email' => 'O e-mail do cliente deve ser válido.',
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
            'sex.in' => 'O sexo deve ser "M" ou "F".',

            // Venda
            'saleData.totalPrice.required' => 'O valor total da venda é obrigatório.',
            'saleData.totalPrice.numeric' => 'O valor total da venda deve ser numérico.',
            'saleData.totalPrice.min' => 'O valor total da venda deve ser no mínimo 0',
            'saleData.products.required' => 'É necessário enviar pelo menos um produto.',
            'saleData.products.array' => 'Os produtos devem ser um array.',
            'saleData.products.*.product_variant_id.required' => 'O ID da variante do produto é obrigatório.',
            'saleData.products.*.product_variant_id.exists' => 'O produto selecionado não existe.',
            'saleData.products.*.newQuantity.required' => 'A quantidade do produto é obrigatória.',
            'saleData.products.*.newQuantity.min' => 'A quantidade do produto deve ser no mínimo 1.',
            'saleData.products.*.stock_quantity.required' => 'A quantidade em estoque é obrigatória.',
            'saleData.products.*.stock_quantity.min' => 'A quantidade em estoque deve ser no mínimo 1.',
            'saleData.products.*.variant_active.required' => 'Deve ser informado se a variante está ativa ou não',

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

            //Pagamento
            'sellerID.exists' => 'O vendedor selecionado não existe.',
            'change.required' => 'O valor do troco é obrigatório.',
            'change.numeric' => 'O valor do troco deve ser numérico.',
            'change.min' => 'O valor do troco não pode ser negativo.',
            'fees.required' => 'O valor das tarifas é obrigatório.',
            'fees.numeric' => 'O valor das tarifas deve ser numérico.',
            'fees.min' => 'O valor das tarifas não pode ser negativo.',
            'payment.required' => 'É necessário enviar pelo menos um pagamento.',
            'payment.array' => 'O pagamento deve ser um array.',
            'payment.*.paymentType.required' => 'O tipo de pagamento é obrigatório.',
            'payment.*.paymentType.exists' => 'O tipo de pagamento selecionado não existe.',
            'payment.*.value.numeric' => 'O valor do pagamento deve ser numérico.',
            'payment.*.value.min' => 'O valor do pagamento não pode ser negativo.',
            'payment.*.receiptID.required' => 'O recebimento é obrigatório.',
            'payment.*.receiptID.exists' => 'O recebimento selecionado não existe.',
            'payment.*.installment.value.integer' => 'O número de parcelas deve ser um inteiro.',
            'payment.*.installment.value.min' => 'O mínimo de parcelas é 2.',
            'payment.*.installment.value.max' => 'O máximo de parcelas é 12.',
            'payment.*.installment.amount.numeric' => 'O valor da parcela deve ser numérico.',
        ];
    }
}
