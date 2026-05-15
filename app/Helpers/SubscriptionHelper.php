<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscriptionHelper
{
    public static function allowTestFree()
    {
        $enterprise = DB::table('enterprises')
            ->where('id', Auth::user()->enterprise_id)
            ->first();

        if ($enterprise->allow_test_free === 0) {
            throw ValidationException::withMessages([
                'test' => ['Esta organização já utilizou seu período de teste gratuito.'],
            ]);
        }
    }
}
