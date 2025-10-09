<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerHelper
{
    public static function existsCode($code)
    {

        $existCode = DB::connection('dalle_adm')->table('sellers')
            ->where('code', $code)
            ->first();

        if (! $existCode) {
            throw ValidationException::withMessages([
                'sellerCode' => ['O código do vendedor informado não existe.'],
            ]);
        }
    }
}
