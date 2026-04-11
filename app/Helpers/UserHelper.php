<?php

namespace App\Helpers;

use App\Models\Permission;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserHelper
{
    public static function validUser($email, $password)
    {
        $userRepository = new UserRepository(new User);
        $user = $userRepository->findByEmail($email);
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha informada está incorreta'],
            ]);
        }
    }

    public static function clearTokenReset($user)
    {
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
    }

    public static function checkPassword($user, $password)
    {
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Credenciais não constam em nosso registro.'],
            ]);
        }
    }

    public static function checkUserActive($user)
    {
        if ($user->active === 0) {
            throw ValidationException::withMessages([
                'active' => ['Este usuário está inativo e não pode acessar a conta. Por favor, entre em contato com o administrador.'],
            ]);
        }
    }

    public static function existsEmail($user, $email)
    {

        $existEmail = DB::table('users')
            ->where('email', $email)
            ->first();

        if ($existEmail) {
            if ($user->email !== $existEmail->email) {
                throw ValidationException::withMessages([
                    'email' => ['Este email ja está em uso.'],
                ]);
            }
        }
    }

    public static function isPasswordEqual($user, $actualPassword)
    {
        if (! Hash::check($actualPassword, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha atual está incorreta.'],
            ]);
        }
    }

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
