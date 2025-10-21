<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleHelper
{
    public static function productValidation($enterpriseId, $productVariantID)
    {
        $existingProduct = DB::table('product_variants')->where('enterpriseID', $enterpriseId)->where('id', $productVariantID)->first();

        if(!$existingProduct){
            throw ValidationException::withMessages([
                    'dataSale.products.*.product_variant_id' => ['O produto selecionado não existe.'],
                ]);
        }

        if($existingProduct && $existingProduct->active === 0){
            throw ValidationException::withMessages([
                    'dataSale.products.*.product_variant_id' => ['O produto selecionado não está ativo.'],
                ]);
        }

        if($existingProduct && $existingProduct->stock_quantity === 0){
            throw ValidationException::withMessages([
                    'dataSale.products.*.stock_quantity' => ['O produto informado está com sua quantidade de estoque 0.'],
                ]);
        }
        if($existingProduct && $newQuantity > $existingProduct->stock_quantity){
            throw ValidationException::withMessages([
                    'dataSale.products.*.stock_quantity' => ['A quantidade do produto informado excede a quantidade que possui em estoque.'],
                ]);
        }
    } 
}
