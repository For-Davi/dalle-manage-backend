<?php

namespace App\Http\Requests\Return;

use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }
}
