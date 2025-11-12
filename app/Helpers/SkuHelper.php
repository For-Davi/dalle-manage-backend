<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SkuHelper
{
    public static function existsSKU($sku, $mode, $variantID = null)
    {
        $existingSku = DB::table('product_variants')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('sku', $sku)
            ->first();

        if ($mode === 'create') {
            if ($existingSku) {
                throw ValidationException::withMessages([
                    'name' => ['Este SKU está sendo utilizado'],
                ]);
            }
        } else {
            if ($existingSku && $existingSku->id !== $variantID) {
                throw ValidationException::withMessages([
                    'name' => ['Este SKU está sendo utilizado'],
                ]);
            }
        }
    }
}
