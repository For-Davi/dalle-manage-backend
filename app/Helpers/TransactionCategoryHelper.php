<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionCategoryHelper
{
    public static function existsCategory($name, $mode, $categoryID = null)
    {
        $existingCategory = DB::table('transaction_categories')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingCategory) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe uma categoria com esse nome.'],
                ]);
            }
        } else {
            if ($existingCategory && $existingCategory->id !== $categoryID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outra categoria com esse nome.'],
                ]);
            }
        }
    }
}
