<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnHelper
{
    public static function existsReturn($saleID, $returnID)
    {
        $return = DB::table('returns')->where('sale_id', $saleID)->where('id', $returnID)->first();

        if(!$return){
            throw ValidationException::withMessages([
                'id.exists' => ['A devolução informada não existe.'],
            ]);
        }
    }

    public static function isSameStatus($returnID, $status)
    {
        $dataStatus = null;

        if($status === 'Ativa'){
            $dataStatus = 'active';
        } else if($status === 'Cancelada') {
            $dataStatus = 'canceled';
        }

        if(!$dataStatus){
            throw ValidationException::withMessages([
                'status.in' => ['Informe um status válido.'],
            ]);
        } else {
            $return = DB::table('returns')->where('id', $returnID)->where('status', $dataStatus)->first();
        }

        if ($return) {
            throw ValidationException::withMessages([
                'status.in' => ['Você não poderá atualizar a devolução com o mesmo status.'],
            ]);
        }
    }
}
