<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductColorHelper
{
    public static function existsColor($name, $mode, $colorId = null)
    {
        $existingColor = DB::table('product_colors')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingColor) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma cor com esse nome.'],
                ]);
            }
        } else {
            if ($existingColor && $existingColor->id !== $colorId) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outra cor com esse nome.'],
                ]);
            }
        }
    }
}
