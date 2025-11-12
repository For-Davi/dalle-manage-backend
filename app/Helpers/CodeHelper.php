<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CodeHelper
{
    public static function existsCode($code, $mode, $variantID = null)
    {
        $existingCode = DB::table('product_variants')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('code', $code)
            ->first();

        if ($mode === 'create') {
            if ($existingCode) {
                throw ValidationException::withMessages([
                    'name' => ['Este código está sendo utilizado'],
                ]);
            }
        } else {
            if ($existingCode && $existingCode->id !== $variantID) {
                throw ValidationException::withMessages([
                    'name' => ['Este código está sendo utilizado'],
                ]);
            }
        }
    }
}
