<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
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

    public static function quantityExceedsQuantityExchangeItem($productVariantID, $saleID, $returnID, $quantity)
    {
        $sale = DB::table('sales')->where('id', $saleID)->where('enterprise_id', Auth::user()->enterprise_id)->first();
        if ($sale) {
            $return = DB::table('returns')->where('sale_id', $sale->id)->where('id', $returnID)->first();

            if ($return) {
                $product = DB::table('return_exchange_items')->where('return_id', $return->id)->where('product_variant_id', $productVariantID)->first();

                if ($product && $quantity > $product->quantity) {
                    throw ValidationException::withMessages([
                        'returnData.products.*.returnQuantity' => ['A quantidade informada de um dos produtos da devolução é maior do que a quantidade trocada.'],
                    ]);
                }
                if (! $product) {
                    throw ValidationException::withMessages([
                        'returnData.products.*.product_variant_id' => ['Algum produto da devolução informado não existe ou não pertence a esta troca.'],
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'returnID' => ['A devolução informada não existe.'],
                ]);
            }

        } else {
            throw ValidationException::withMessages([
                'saleID' => ['A venda informada não existe.'],
            ]);
        }
    }
}
