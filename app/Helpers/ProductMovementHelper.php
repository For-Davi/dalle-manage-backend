<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductMovementHelper
{
    public static function validateStockReentryQuantity($quantity, $productVariantID, $enterpriseID)
    {
        $existingProduct = DB::table('stock_reentries_return_products')
            ->where('enterprise_id', $enterpriseID)
            ->where('product_variant_id', $productVariantID)
            ->first();

        if ($existingProduct->quantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => ['A quantidade informada excede a quantidade devolvida.'],
            ]);
        }
    }
}
