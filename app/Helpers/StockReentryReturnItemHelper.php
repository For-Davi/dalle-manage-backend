<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class StockReentryReturnItemHelper
{
    public static function existsProduct($productVariantID)
    {
        $existingProduct = DB::table('stock_reentries_return_products')->where('product_variant_id', $productVariantID)->first();

        if (! $existingProduct) {
            return null;
        }

        return $existingProduct;

    }
}
