<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientHelper
{
    public static function existsClient($id, string $field = 'clientID')
    {
        if ($id) {
            $client = DB::table('clients')
                ->where('id', $id)
                ->where('enterprise_id', Auth::user()->enterprise_id)
                ->first();

            if ($client) {
                return true;
            } else {
                throw ValidationException::withMessages([
                    $field => ['O cliente informado não existe.'],
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                $field => ['O cliente informado não existe.'],
            ]);
        }
    }

    public static function validateCredit($id, $credit, string $field)
    {
        $client = DB::table('clients')
            ->where('id', $id)
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->first();

        if ($credit) {
            if ($client->credits === $credit) {
                return true;
            } else {
                throw ValidationException::withMessages([
                    $field => ['A quantidade de crédito informada é inválida.'],
                ]);
            }
        }
    }
}
