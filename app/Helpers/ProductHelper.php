<?php

namespace App\Helpers;

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
}
