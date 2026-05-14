<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerHelper
{
    public static function existsPhone($phone)
    {

        $existPhone = DB::connection('dalle_manage_adm')->table('sellers')
            ->where('phone', $phone)
            ->first();

        if ($existPhone) {
            throw ValidationException::withMessages([
                'sellerCode' => ['O telefone informado já está sendo utilizado.'],
            ]);
        }
    }

    public static function existsCode($code)
    {

        $existCode = DB::connection('dalle_manage_adm')->table('sellers')
            ->where('code', $code)
            ->first();

        if (! $existCode) {
            throw ValidationException::withMessages([
                'sellerCode' => ['O código informado não existe.'],
            ]);
        }
    }

    public static function existsEmail($email, $mode, $sellerID = null)
    {

        $existEmail = DB::connection('dalle_manage_adm')->table('sellers')
            ->where('email', $email)
            ->first();

        if ($existEmail) {
            throw ValidationException::withMessages([
                'sellerEmail' => ['O e-mail informado já está sendo utilizado.'],
            ]);
        }
    }
}
