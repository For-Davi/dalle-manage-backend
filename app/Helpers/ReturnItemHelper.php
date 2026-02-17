<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnItemHelper
{
    public static function quantityExceedsQuantitySale($productVariantID, $saleID, $quantity)
    {
        $product = DB::table('sale_itens')->where('sale_id', $saleID)->where('product_variant_id', $productVariantID)->first();

        if ($product && $quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'returnData.products.*.returnQuantity' => ['A quantidade informada de um dos produtos da devolução é maior do que a quantidade comprada.'],
            ]);
        }
        if (! $product) {
            throw ValidationException::withMessages([
                'returnData.products.*.product_variant_id' => ['Algum produto da devolução informado não pertence a esta venda.'],
            ]);
        }
    }

    public static function quantityExceedsQuantityExchangeItem($productVariantID, $returnID, $quantity)
    {
        $product = DB::table('return_exchange_items')->where('return_id', $returnID)->where('product_variant_id', $productVariantID)->first();

        if ($product && $quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'returnData.products.*.returnQuantity' => ['A quantidade informada de um dos produtos da devolução é maior do que a quantidade trocada.'],
            ]);
        }
        if (! $product) {
            throw ValidationException::withMessages([
                'returnData.products.*.product_variant_id' => ['Algum produto da devolução informado não pertence a esta troca.'],
            ]);
        }
    }
}
