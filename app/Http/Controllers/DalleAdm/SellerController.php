<?php

namespace App\Http\Controllers\DalleAdm;

use App\DTO\DalleAdm\Seller\FilterDashboardDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\DalleAdm\Seller\FilterDashboardRequest;
use App\Http\Requests\DalleAdm\Seller\LoginSellerRequest;
use App\Http\Requests\DalleAdm\Seller\UpdateSellerDataRequest;
use App\Http\Requests\DalleAdm\Seller\UpdateSellerPasswordRequest;
use App\Http\Requests\User\NewPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Resources\DalleAdm\Seller\SellerResource;
use App\Repositories\DalleAdm\CommissionRepository;
use App\Services\DalleAdm\SellerService;
use Carbon\Carbon;

class SellerController extends BaseController
{
    public function __construct(
        protected SellerService $service,
        protected CommissionRepository $commissionRepository,
    ) {}

    private function configureToken($seller)
    {
        $token = $seller->createToken('my-app-token');
        $token->accessToken->update([
            'expires_at' => Carbon::now()->addHours(3),
        ]);

        return $token->plainTextToken;
    }

    public function login(LoginSellerRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $seller = $this->service->login($request);
            $token = $this->configureToken($seller);

            return response()->json([
                'seller' => new SellerResource($seller),
                'token' => $token,
            ], 200);
        }, 'Erro ao logar como associado', $request);
    }

    public function dashboard(FilterDashboardRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $filterDashboardDTO = FilterDashboardDTO::fromRequest($request);
            $dashboard = $this->commissionRepository->getAllWithFilter($filterDashboardDTO->toArray());

            return response()->json(['dashboard' => $dashboard], 200);
        }, 'Erro ao pegar dados do dashboard', $request);
    }

    public function updateData(UpdateSellerDataRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $seller = $this->service->updateData($request);

            return response()->json([
                'seller' => new SellerResource($seller),
                'message' => 'Dados atualizados',
            ], 200);
        }, 'Erro ao atualizar dados do associado', $request);
    }

    public function updatePassword(UpdateSellerPasswordRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $this->service->updatePassword($request);

            return response()->json([
                'message' => 'Senha atualizada',
            ], 200);
        }, 'Erro ao atualizar senha do associado', $request);
    }

    public function reset(ResetPasswordRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $result = $this->service->reset($request);

            return response()->json(['message' => $result], 200);
        }, 'Erro ao solicitar redefinição de senha', $request);
    }

    public function newPassword(NewPasswordRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->newPassword($request);

            return response()->json(['message' => 'Sua senha foi redefinida'], 200);
        }, 'Erro ao redefinir senha', $request);
    }
}
