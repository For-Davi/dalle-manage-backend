<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TypeReceiptHelper
{
    public static function existsType($name, $mode, $typeID = null)
    {
        $existingType = DB::table('types_receipt')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingType) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um tipo com esse nome.'],
                ]);
            }
        } else {
            if ($existingType && $existingType->id !== $typeID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro tipo com esse nome.'],
                ]);
            }
        }
    }
}
