<?php

namespace App\Helpers;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermissionHelper
{
    public static function hasPermission($permissionSlug)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasPermission($permissionSlug)) {

            $permissionData = Permission::where('slug', $permissionSlug)->first();

            $description = $permissionData
                ? $permissionData->description
                : "ação específica ({$permissionSlug})";

            throw ValidationException::withMessages([
                'permission' => ["Acesso negado! Você não possui permissão para: {$description}."],
            ]);
        }
    }
}
