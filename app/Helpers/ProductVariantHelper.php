<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductVariantHelper
{
    public static function existsProductVariant($productVariantID)
    {
        $existingProduct = DB::table('product_variants')->where('id', $productVariantID)->first();

        if (! $existingProduct) {
            throw ValidationException::withMessages([
                'dataSale.products.*.productVariantID' => ['O produto selecionado não existe.'],
            ]);
        }
    }

    public static function isProductVariantActive($productVariantID)
    {
        $product = DB::table('product_variants')->where('id', $productVariantID)->first();

        if ($product && $product->active === 0) {
            throw ValidationException::withMessages([
                'dataSale.products.*.productVariantID' => ['O produto selecionado não está ativo.'],
            ]);
        }
    }

    public static function hasProductVariantStock($productVariantID)
    {
        $product = DB::table('product_variants')->where('id', $productVariantID)->first();

        if ($product && $product->stock_quantity === 0) {
            throw ValidationException::withMessages([
                'dataSale.products.*.newQuantity' => ['O produto informado está com sua quantidade de estoque 0.'],
            ]);
        }
    }

    public static function quantityExceedsStock($productVariantID, $quantity)
    {
        $product = DB::table('product_variants')->where('id', $productVariantID)->first();

        if ($product && $quantity > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'dataSale.products.*.newQuantity' => ['A quantidade informada do produto excede a quantidade que ele possui no estoque.'],
            ]);
        }
    }

    public static function getUsedCodes(array $codes): array
    {
        return DB::table('product_variants')
            ->whereIn('code', $codes)
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->pluck('code')
            ->toArray();
    }

    public static function getUsedSkus(array $skus): array
    {
        return DB::table('product_variants')
            ->whereIn('sku', $skus)
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->pluck('sku')
            ->toArray();
    }
}
