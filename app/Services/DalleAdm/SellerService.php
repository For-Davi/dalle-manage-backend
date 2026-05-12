<?php

namespace App\Services\DalleAdm;

use App\DTO\DalleAdm\Seller\UpdateSellerDataDTO;
use App\DTO\DalleAdm\Seller\UpdateSellerPasswordDTO;
use App\Helpers\DalleAdm\SellerHelper;
use App\Helpers\TokenHelper;
use App\Jobs\Email\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Repositories\DalleAdm\SellerRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SellerService
{
    public function __construct(
        protected SellerRepository $repository,
    ) {}

    public function login($request)
    {
        if ($request->token) {
            $seller = TokenHelper::findTokenCustom($request->token, 'dalle_manage_adm');
        } else {
            $seller = $this->repository->findByCpfWithoutCache($request->cpf);
        }

        $this->hasUser($seller);
        if (! $request->token) {
            SellerHelper::checkPassword($seller, $request->password);
        }
        SellerHelper::clearTokenReset($seller);

        return $seller;
    }

    public function updateData($request)
    {
        $updateSellerDataDTO = UpdateSellerDataDTO::fromRequest($request);

        return $this->repository->update(Auth::user()->id, $updateSellerDataDTO->toArray());
    }

    public function updatePassword($request)
    {
        SellerHelper::isPasswordEqual($request->user(), $request->currentPassword);

        $updateSellerPasswordDTO = UpdateSellerPasswordDTO::fromRequest($request);

        return $this->repository->update(Auth::user()->id, $updateSellerPasswordDTO->toArray());
    }

    public function reset($request)
    {
        $seller = $this->repository->findByEmail($request->input('email'));

        if ($seller) {
            $token = app('auth.password.broker')->createToken($seller);
            $this->setTypePassword($seller->email, 'reset');
            SendResetPasswordEmail::dispatch($seller, $token, true);
        }

        return 'Caso o e-mail esteja em nosso cadastro, você receberá as instruções para redefinição de senha.';
    }

    public function newPassword($request)
    {
        $register = PasswordResetToken::where('token', $request->input('token'))
            ->first();

        if (! $register) {
            return response()->json(['error' => 'Token inválido.'], 400);
        }

        if ($register->type === 'reset') {

            $isExpired = Carbon::parse($register->created_at)->addMinutes(30)->isPast();

            if ($isExpired) {
                throw ValidationException::withMessages([
                    'token' => ['Token expirado'],
                ]);
            }

        }

        $data = ['password' => Hash::make($request->input('password'))];
        $result = $this->repository->newPassword($register->email, $data);

        $register->delete();

        return $result;
    }

    private function hasUser($seller)
    {
        if (! $seller) {
            throw ValidationException::withMessages([
                'cpf' => ['Credenciais não constam em nosso registro.'],
            ]);
        }
    }

    private function setTypePassword($email, $type)
    {
        $resetRecord = PasswordResetToken::where('email', $email)
            ->latest()
            ->first();
        if ($resetRecord) {
            $resetRecord->update(['type' => $type]);
        }
    }
}
