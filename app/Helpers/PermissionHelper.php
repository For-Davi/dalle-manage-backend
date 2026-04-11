<?php

namespace App\Helpers;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermissionHelper
{
    public static function hasPermissions(array $slugs)
    {
        $user = Auth::user();

        foreach ($slugs as $slug) {
            if (! $user || ! $user->hasPermission($slug)) {

                $permissionData = Permission::where('slug', $slug)->first();
                $description = $permissionData ? $permissionData->description : "ação ({$slug})";

                throw ValidationException::withMessages([
                    'permission' => ["Acesso negado! Você precisa da permissão: {$description}."],
                ]);
            }
        }
    }
}
