<?php

namespace App\Helpers\DalleAdm;

use App\Models\DalleAdm\Seller;
use App\Repositories\DalleAdm\SellerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SellerHelper
{
    public static function validUser($cpf, $password)
    {
        $sellerRepository = new SellerRepository(new Seller);
        $seller = $sellerRepository->findByCpf($cpf);
        if (! Hash::check($password, $seller->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha informada está incorreta'],
            ]);
        }
    }

    public static function clearTokenReset($seller)
    {
        DB::connection('dalle_manage_adm')->table('password_reset_tokens')->where('email', $seller->email)->delete();
        DB::connection('dalle_manage_adm')->table('personal_access_tokens')->where('tokenable_id', $seller->id)->delete();
    }

    public static function checkPassword($seller, $password)
    {
        if (! Hash::check($password, $seller->password)) {
            throw ValidationException::withMessages([
                'password' => ['Credenciais não constam em nosso registro.'],
            ]);
        }
    }

    public static function existsEmail($seller, $email)
    {

        $existEmail = DB::connection('dalle_manage_adm')->table('users')
            ->where('email', $email)
            ->first();

        if ($existEmail) {
            if ($seller->email !== $existEmail->email) {
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
}
