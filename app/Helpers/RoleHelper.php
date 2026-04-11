<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleHelper
{
    public static function existsRole($name, $mode, $roleID = null)
    {
        $existingRole = DB::table('roles')
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->where('name', $name)
            ->first();

        if ($mode === 'create') {
            if ($existingRole) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um perfil com esse nome.'],
                ]);
            }
        } else {
            if ($existingRole && $existingRole->id !== $roleID) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe outro perfil com esse nome.'],
                ]);
            }
        }
    }
}
