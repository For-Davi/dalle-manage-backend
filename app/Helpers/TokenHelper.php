<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class TokenHelper
{
    public static function findTokenCustom(string $token, string $connection, string $table = 'personal_access_tokens')
    {
        if (strpos($token, '|') === false) {
            return DB::connection($connection)
                ->table($table)
                ->where('token', hash('sha256', $token))
                ->first();
        }

        [$id, $plainToken] = explode('|', $token, 2);

        $instance = DB::connection($connection)
            ->table($table)
            ->where('id', $id)
            ->first();

        $accessToken = null;

        if ($instance) {
            $accessToken = hash_equals($instance->token, hash('sha256', $plainToken)) ? $instance : null;
        }

        if (! $accessToken) {
            throw new \Exception('Token inválido ou expirado.');
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            DB::connection($connection)
                ->table('personal_access_tokens')
                ->where('id', $accessToken->id)
                ->delete();
            throw new \Exception('O token expirou. Faça login novamente.');
        }

        return $accessToken->tokenable;
    }
}
