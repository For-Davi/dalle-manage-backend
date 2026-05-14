<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductHelper
{
    public static function existsProduct($name, $mode, $productID = null)
    {
        $existingProduct = DB::table('products')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingProduct) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um produto com esse nome.'],
                ]);
            }
        } else {
            if ($existingProduct && $existingProduct->id !== $productID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro produto com esse nome.'],
                ]);
            }
        }
    }

    public static function validateDiscount($productID, $discount)
    {
        $existingProduct = DB::table('products')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('id', $productID)
            ->first();

        $existinProductAdvanced = DB::table('product_advanced')
            ->where('product_id', $productID)
            ->first();

        if ($discount > 0 && ! $existinProductAdvanced->allow_discount) {
            throw ValidationException::withMessages([
                'discount' => ["O produto {$existingProduct->name} não permite a aplicação de desconto."],
            ]);
        }

        if ($discount > $existinProductAdvanced->discount_max_percentage) {
            throw ValidationException::withMessages([
                'discount' => ["O produto {$existingProduct->name} deve ter o desconto máximo de {$existinProductAdvanced->discount_max_percentage}%."],
            ]);
        }
    }
}
